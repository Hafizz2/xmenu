<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cafe;
use App\Models\Event;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Support\Carbon;

class OverviewController extends Controller
{
    public function index()
    {
        $now = now();
        $statusCounts = Cafe::selectRaw('status, count(*) n')->groupBy('status')->pluck('n', 'status');
        $totalCafes = $statusCounts->sum();

        $newThisMonth = Cafe::where('created_at', '>=', $now->copy()->startOfMonth())->count();
        $newLastMonth = Cafe::whereBetween('created_at', [$now->copy()->subMonthNoOverflow()->startOfMonth(), $now->copy()->subMonthNoOverflow()->endOfMonth()])->count();

        $approved = Subscription::where('status', 'approved');
        $revThisMonth = (clone $approved)->where('approved_at', '>=', $now->copy()->startOfMonth())->sum('amount');
        $revLastMonth = (clone $approved)->whereBetween('approved_at', [$now->copy()->subMonthNoOverflow()->startOfMonth(), $now->copy()->subMonthNoOverflow()->endOfMonth()])->sum('amount');
        $revTotal = (clone $approved)->sum('amount');

        $mrr = static::mrr();
        $pending = Subscription::with('cafe')->where('status', 'pending')->latest()->limit(6)->get();
        $pendingCount = Subscription::where('status', 'pending')->count();

        $scans30 = Event::where('type', 'menu_view')->where('created_at', '>=', $now->copy()->subDays(30))->count();
        $games30 = Event::where('type', 'game_open')->where('created_at', '>=', $now->copy()->subDays(30))->count();
        $liveCafes = Event::where('type', 'menu_view')->where('created_at', '>=', $now->copy()->subDays(7))->distinct('cafe_id')->count('cafe_id');

        $signups = static::daily(Cafe::query(), 'created_at', 30);
        $scans = static::daily(Event::where('type', 'menu_view'), 'created_at', 30);
        $revenue = collect(range(11, 0))->map(function ($ago) use ($now) {
            $m = $now->copy()->startOfMonth()->subMonthsNoOverflow($ago);

            return [
                'label' => $m->format('M Y'),
                'short' => $m->format('M'),
                'n' => (float) Subscription::where('status', 'approved')->whereBetween('approved_at', [$m, $m->copy()->endOfMonth()])->sum('amount'),
            ];
        });

        $recent = Cafe::with('owner')->withCount('items')->latest()->limit(8)->get();
        $expiring = Cafe::where(function ($q) use ($now) {
            $q->where(fn ($w) => $w->where('status', 'trial')->whereBetween('trial_ends_at', [$now, $now->copy()->addDays(7)]))
                ->orWhere(fn ($w) => $w->where('status', 'active')->whereBetween('paid_until', [$now, $now->copy()->addDays(7)]));
        })->orderByRaw('COALESCE(paid_until, trial_ends_at)')->limit(8)->get();

        // To-do list: things that need the SaaS owner today.
        $overdue = Cafe::with('owner')->where('status', 'overdue')->orderBy('paid_until')->limit(8)->get();
        $stuck = Cafe::with('owner')->whereIn('status', ['trial', 'active'])->where('created_at', '<', $now->copy()->subDays(2))
            ->whereDoesntHave('items')->latest()->limit(6)->get();

        return view('admin.overview', compact(
            'statusCounts', 'totalCafes', 'newThisMonth', 'newLastMonth', 'revThisMonth', 'revLastMonth', 'revTotal',
            'mrr', 'pending', 'pendingCount', 'scans30', 'games30', 'liveCafes', 'signups', 'scans', 'revenue', 'recent', 'expiring',
            'overdue', 'stuck'
        ));
    }

    /** Monthly recurring revenue: paying cafés × their plan's monthly price. */
    public static function mrr(): float
    {
        $prices = Plan::pluck('price', 'key');

        return (float) Cafe::where('status', 'active')->where('paid_until', '>', now())
            ->selectRaw('plan, count(*) n')->groupBy('plan')->get()
            ->sum(fn ($r) => ($prices[$r->plan] ?? 0) * $r->n);
    }

    /** Count rows per day for the last $days days. */
    public static function daily($query, string $col, int $days)
    {
        $rows = (clone $query)->where($col, '>=', now()->subDays($days - 1)->startOfDay())
            ->selectRaw("DATE($col) d, count(*) n")->groupBy('d')->pluck('n', 'd');

        return collect(range($days - 1, 0))->map(function ($ago) use ($rows) {
            $d = Carbon::today()->subDays($ago);

            return ['label' => $d->format('D j M'), 'short' => $d->format('j'), 'n' => (int) ($rows[$d->toDateString()] ?? 0)];
        });
    }
}
