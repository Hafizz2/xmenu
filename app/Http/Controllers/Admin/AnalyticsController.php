<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cafe;
use App\Models\Event;
use App\Models\Game;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $days = in_array((int) $request->query('days'), [7, 30, 90], true) ? (int) $request->query('days') : 30;
        $since = now()->subDays($days - 1)->startOfDay();
        $events = Event::where('created_at', '>=', $since);

        $totals = (clone $events)->selectRaw('type, count(*) n')->groupBy('type')->pluck('n', 'type');
        $scansDaily = OverviewController::daily(Event::where('type', 'menu_view'), 'created_at', $days);
        $signupsDaily = OverviewController::daily(Cafe::query(), 'created_at', $days);

        $views = (int) ($totals['menu_view'] ?? 0);
        $gameRate = $views ? round(($totals['game_open'] ?? 0) / $views * 100, 1) : 0;
        $tableScans = (clone $events)->where('type', 'menu_view')->whereNotNull('table_no')->count();

        $topCafes = (clone $events)->where('type', 'menu_view')->selectRaw('cafe_id, count(*) n')->groupBy('cafe_id')
            ->orderByDesc('n')->limit(10)->get()->map(fn ($r) => ['cafe' => Cafe::find($r->cafe_id), 'n' => $r->n])->filter(fn ($r) => $r['cafe']);

        $topGames = (clone $events)->where('type', 'game_open')->whereNotNull('game_id')->selectRaw('game_id, count(*) n')->groupBy('game_id')
            ->orderByDesc('n')->limit(10)->get()->map(fn ($r) => ['game' => Game::find($r->game_id), 'n' => $r->n])->filter(fn ($r) => $r['game']);

        $playTime = (clone $events)->where('type', 'game_close')->whereRaw('value REGEXP \'^[0-9]+$\'')
            ->selectRaw('AVG(CAST(value AS UNSIGNED)) avg_s')->value('avg_s');

        $topSearches = (clone $events)->where('type', 'search')->whereNotNull('value')
            ->selectRaw('LOWER(value) v, count(*) n')->groupBy('v')->orderByDesc('n')->limit(10)->pluck('n', 'v');

        $filters = (clone $events)->where('type', 'filter_used')->selectRaw('value v, count(*) n')->groupBy('v')->orderByDesc('n')->pluck('n', 'v');

        // Activation funnel over all cafés that signed up in the period.
        $cohort = Cafe::where('created_at', '>=', $since)->pluck('id');
        $c = $cohort->count();
        $withItems = Item::whereIn('cafe_id', $cohort)->select('cafe_id')->groupBy('cafe_id')->havingRaw('count(*) >= 3')->get()->count();
        $designed = Cafe::whereIn('id', $cohort)->where('theme', 'like', '%"_saved":true%')->count();
        $qr = Cafe::whereIn('id', $cohort)->where('qr_settings', 'like', '%downloaded_at%')->count();
        $scanned = Event::whereIn('cafe_id', $cohort)->where('type', 'menu_view')->distinct('cafe_id')->count('cafe_id');
        $paid = Cafe::whereIn('id', $cohort)->whereNotNull('paid_until')->count();
        $funnel = [
            ['Signed up', $c], ['Added 3+ items', $withItems], ['Published a design', $designed],
            ['Downloaded QR codes', $qr], ['Got first scan', $scanned], ['Paid', $paid],
        ];

        $hours = (clone $events)->where('type', 'menu_view')->selectRaw('HOUR(created_at) h, count(*) n')->groupBy('h')->pluck('n', 'h');
        $hourly = collect(range(0, 23))->map(fn ($h) => ['label' => sprintf('%02d:00', $h), 'short' => $h % 3 === 0 ? (string) $h : '', 'n' => (int) ($hours[$h] ?? 0)]);

        return view('admin.analytics', compact('days', 'totals', 'scansDaily', 'signupsDaily', 'views', 'gameRate', 'tableScans',
            'topCafes', 'topGames', 'playTime', 'topSearches', 'filters', 'funnel', 'hourly'));
    }
}
