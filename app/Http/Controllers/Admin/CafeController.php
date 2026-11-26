<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminLog;
use App\Models\Cafe;
use App\Models\Event;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class CafeController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q'));
        $status = $request->query('status');
        $plan = $request->query('plan');
        $sort = $request->query('sort', 'new');

        $scanSub = Event::selectRaw('count(*)')->whereColumn('events.cafe_id', 'cafes.id')
            ->where('type', 'menu_view')->where('created_at', '>=', now()->subDays(30));

        $cafes = Cafe::with('owner')->withCount('items')
            ->addSelect(['scans_30' => $scanSub])
            ->when($q, fn ($query) => $query->where(fn ($w) => $w->where('name', 'like', "%$q%")->orWhere('slug', 'like', "%$q%")
                ->orWhereHas('owner', fn ($o) => $o->where('email', 'like', "%$q%")->orWhere('name', 'like', "%$q%")->orWhere('phone', 'like', "%$q%"))))
            ->when(in_array($status, Cafe::STATUSES, true), fn ($query) => $query->where('status', $status))
            ->when($plan, fn ($query) => $query->where('plan', $plan))
            ->when($sort === 'scans', fn ($query) => $query->orderByDesc('scans_30'))
            ->when($sort === 'name', fn ($query) => $query->orderBy('name'))
            ->when($sort === 'expiry', fn ($query) => $query->orderByRaw('COALESCE(paid_until, trial_ends_at) asc'))
            ->when($sort === 'new', fn ($query) => $query->latest())
            ->paginate(25)->withQueryString();

        $counts = Cafe::selectRaw('status, count(*) n')->groupBy('status')->pluck('n', 'status');
        $plans = Plan::orderBy('sort_order')->get();

        return view('admin.cafes.index', compact('cafes', 'counts', 'q', 'status', 'plan', 'sort', 'plans'));
    }

    public function show(Cafe $cafe)
    {
        $cafe->load(['owner', 'subscriptions.approver'])->loadCount(['items', 'categories', 'paymentAccounts']);
        $since = now()->subDays(29)->startOfDay();
        $totals = Event::where('cafe_id', $cafe->id)->where('created_at', '>=', $since)
            ->selectRaw('type, count(*) n')->groupBy('type')->pluck('n', 'type');
        $daily = OverviewController::daily(Event::where('cafe_id', $cafe->id)->where('type', 'menu_view'), 'created_at', 30);
        $lastScan = Event::where('cafe_id', $cafe->id)->where('type', 'menu_view')->max('created_at');
        $logs = AdminLog::with('user')->where('cafe_id', $cafe->id)->latest('id')->limit(15)->get();
        $plans = Plan::orderBy('sort_order')->get();
        $revenue = $cafe->subscriptions->where('status', 'approved')->sum('amount');

        return view('admin.cafes.show', compact('cafe', 'totals', 'daily', 'lastScan', 'logs', 'plans', 'revenue'));
    }

    public function update(Request $request, Cafe $cafe)
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(Cafe::STATUSES)],
            'plan' => ['required', 'string', Rule::exists('plans', 'key')],
            'trial_ends_at' => ['nullable', 'date'],
            'paid_until' => ['nullable', 'date'],
            'admin_notes' => ['nullable', 'string', 'max:5000'],
        ]);
        $before = $cafe->only(['status', 'plan', 'trial_ends_at', 'paid_until']);
        $cafe->status = $data['status'];
        $cafe->plan = $data['plan'];
        $cafe->trial_ends_at = $data['trial_ends_at'] ? Carbon::parse($data['trial_ends_at'])->endOfDay() : null;
        $cafe->paid_until = $data['paid_until'] ? Carbon::parse($data['paid_until'])->endOfDay() : null;
        $cafe->admin_notes = $data['admin_notes'] ?? null;
        $cafe->save();

        AdminLog::record('cafe.update', $cafe->id, ['before' => $before, 'after' => $cafe->only(['status', 'plan', 'trial_ends_at', 'paid_until'])]);

        return back()->with('ok', "{$cafe->name} updated.");
    }

    public function extendTrial(Request $request, Cafe $cafe)
    {
        $days = (int) $request->validate(['days' => ['required', 'integer', 'min:1', 'max:365']])['days'];
        $from = $cafe->trial_ends_at && $cafe->trial_ends_at->isFuture() ? $cafe->trial_ends_at : now();
        $cafe->trial_ends_at = $from->copy()->addDays($days);
        if (in_array($cafe->status, ['overdue', 'trial'], true) && ! ($cafe->paid_until && $cafe->paid_until->isFuture())) {
            $cafe->status = 'trial';
        }
        $cafe->save();
        AdminLog::record('cafe.extend_trial', $cafe->id, ['days' => $days]);

        return back()->with('ok', "Trial extended by {$days} days.");
    }

    public function suspend(Cafe $cafe)
    {
        $cafe->status = $cafe->status === 'suspended'
            ? ($cafe->paid_until && $cafe->paid_until->isFuture() ? 'active' : ($cafe->trial_ends_at && $cafe->trial_ends_at->isFuture() ? 'trial' : 'overdue'))
            : 'suspended';
        $cafe->save();
        AdminLog::record($cafe->status === 'suspended' ? 'cafe.suspend' : 'cafe.unsuspend', $cafe->id);

        return back()->with('ok', $cafe->status === 'suspended' ? 'Menu suspended. Customers see “unavailable”.' : 'Menu is live again.');
    }

    /** Enter "manage mode": edit this café's content from its own dashboard, without the owner's password. */
    public function manage(Request $request, Cafe $cafe)
    {
        $request->session()->put('managing_cafe', $cafe->id);
        AdminLog::record('cafe.manage_start', $cafe->id);

        return redirect()->route('dashboard')->with('ok', "You’re now editing {$cafe->name}.");
    }

    public function stopManage(Request $request)
    {
        $id = $request->session()->pull('managing_cafe');
        if ($id) {
            AdminLog::record('cafe.manage_stop', $id);
        }

        return $id ? redirect()->route('admin.cafes.show', $id) : redirect()->route('admin.index');
    }

    public function destroy(Request $request, Cafe $cafe)
    {
        $request->validate(['confirm' => ['required', 'in:'.$cafe->slug]], ['confirm.in' => 'Type the café link exactly to confirm.']);
        $name = $cafe->name;
        AdminLog::record('cafe.delete', $cafe->id, ['name' => $name, 'slug' => $cafe->slug, 'owner' => $cafe->owner?->email]);
        Storage::disk('public')->deleteDirectory("cafes/{$cafe->id}");
        $cafe->delete();

        return redirect()->route('admin.cafes.index')->with('ok', "{$name} was deleted.");
    }
}
