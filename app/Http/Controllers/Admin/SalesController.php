<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminLog;
use App\Models\Cafe;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class SalesController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'pending');
        $q = trim((string) $request->query('q'));

        $subs = Subscription::with(['cafe.owner', 'approver'])
            ->when(in_array($status, ['pending', 'approved', 'rejected'], true), fn ($query) => $query->where('status', $status))
            ->when($q, fn ($query) => $query->where(fn ($w) => $w->where('reference', 'like', "%$q%")->orWhereHas('cafe', fn ($c) => $c->where('name', 'like', "%$q%"))))
            ->latest()->paginate(25)->withQueryString();

        $counts = Subscription::selectRaw('status, count(*) n')->groupBy('status')->pluck('n', 'status');

        $approved = Subscription::where('status', 'approved');
        $summary = [
            'mrr' => OverviewController::mrr(),
            'month' => (clone $approved)->where('approved_at', '>=', now()->startOfMonth())->sum('amount'),
            'year' => (clone $approved)->where('approved_at', '>=', now()->startOfYear())->sum('amount'),
            'total' => (clone $approved)->sum('amount'),
            'paying' => Cafe::where('status', 'active')->where('paid_until', '>', now())->count(),
        ];
        $summary['arpu'] = $summary['paying'] ? $summary['mrr'] / $summary['paying'] : 0;

        $byPlan = (clone $approved)->selectRaw('plan, count(*) n, sum(amount) total')->groupBy('plan')->get();
        $byMethod = (clone $approved)->selectRaw('method, count(*) n, sum(amount) total')->groupBy('method')->get();

        $plans = Plan::orderBy('sort_order')->get();
        $cafes = Cafe::orderBy('name')->get(['id', 'name', 'slug']);

        return view('admin.sales', compact('subs', 'counts', 'status', 'q', 'summary', 'byPlan', 'byMethod', 'plans', 'cafes'));
    }

    /** Record a payment received outside the app (cash, transfer). Approved straight away. */
    public function store(Request $request)
    {
        $data = $request->validate([
            'cafe_id' => ['required', 'exists:cafes,id'],
            'plan' => ['required', Rule::exists('plans', 'key')],
            'months' => ['required', 'integer', 'min:1', 'max:36'],
            'amount' => ['required', 'numeric', 'min:0'],
            'method' => ['required', Rule::in(array_keys(Subscription::METHODS))],
            'reference' => ['nullable', 'string', 'max:120'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);
        $sub = new Subscription($data + ['currency' => Plan::where('key', $data['plan'])->value('currency') ?? 'ETB', 'status' => 'pending']);
        $sub->cafe_id = $data['cafe_id'];
        $sub->save();
        $sub->approve($request->user());
        AdminLog::record('sales.record_payment', $sub->cafe_id, ['subscription' => $sub->id, 'amount' => $sub->amount, 'months' => $sub->months]);

        return back()->with('ok', 'Payment recorded. '.$sub->cafe->name.' is paid until '.$sub->period_end->format('j M Y').'.');
    }

    public function approve(Request $request, Subscription $subscription)
    {
        abort_unless($subscription->status === 'pending', 422);
        $subscription->approve($request->user());
        AdminLog::record('sales.approve', $subscription->cafe_id, ['subscription' => $subscription->id, 'amount' => $subscription->amount]);

        return back()->with('ok', 'Approved. '.$subscription->cafe->name.' is active until '.$subscription->period_end->format('j M Y').'.');
    }

    public function reject(Request $request, Subscription $subscription)
    {
        abort_unless($subscription->status === 'pending', 422);
        $subscription->status = 'rejected';
        $subscription->note = $request->input('note') ?: 'Payment could not be confirmed.';
        $subscription->save();
        AdminLog::record('sales.reject', $subscription->cafe_id, ['subscription' => $subscription->id, 'note' => $subscription->note]);

        return back()->with('ok', 'Payment rejected. The café sees your note.');
    }

    public function receipt(Subscription $subscription)
    {
        abort_unless($subscription->receipt && Storage::disk('local')->exists($subscription->receipt), 404);

        return Storage::disk('local')->response($subscription->receipt);
    }

    // ---------- Plans ----------
    public function plans()
    {
        return view('admin.plans', ['plans' => Plan::orderBy('sort_order')->get()]);
    }

    public function updatePlan(Request $request, Plan $plan)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:60'],
            'tagline' => ['nullable', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'max:10'],
            'max_tables' => ['nullable', 'integer', 'min:1'],
            'features' => ['nullable', 'string', 'max:2000'],
            'active' => ['nullable', 'boolean'],
        ]);
        $before = $plan->only(['name', 'price', 'active']);
        $plan->update([
            'name' => $data['name'],
            'tagline' => $data['tagline'] ?? null,
            'price' => $data['price'],
            'currency' => strtoupper($data['currency']),
            'max_tables' => $data['max_tables'] ?? null,
            'features' => collect(preg_split('/\r?\n/', $data['features'] ?? ''))->map('trim')->filter()->values()->all(),
            'active' => $request->boolean('active'),
        ]);
        AdminLog::record('plan.update', null, ['plan' => $plan->key, 'before' => $before, 'after' => $plan->only(['name', 'price', 'active'])]);

        return back()->with('ok', "{$plan->name} plan saved.");
    }
}
