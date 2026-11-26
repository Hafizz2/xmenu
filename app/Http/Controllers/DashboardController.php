<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Item;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $cafe = $this->cafe();
        $itemCount = $cafe->items()->count();

        $steps = [
            ['done' => (bool) $cafe->logo, 'title' => 'Add your logo and café details', 'text' => 'Logo, phone, address, hours and Wi-Fi.', 'route' => 'dashboard.info', 'cta' => 'Add details'],
            ['done' => $itemCount >= 3, 'title' => 'Build your menu', 'text' => 'Categories, items, prices and photos. Or import a spreadsheet.', 'route' => 'dashboard.menu', 'cta' => 'Add items'],
            ['done' => ! empty($cafe->theme['_saved']), 'title' => 'Make it yours', 'text' => 'Pick your colour, layout and font.', 'route' => 'dashboard.design', 'cta' => 'Open design studio'],
            ['done' => $cafe->paymentAccounts()->exists(), 'title' => 'Add how customers pay', 'text' => 'Bank accounts, Telebirr, CBE Birr.', 'route' => 'dashboard.payments', 'cta' => 'Add accounts'],
            ['done' => ! empty($cafe->qr_settings['downloaded_at']), 'title' => 'Download your QR codes', 'text' => 'Choose a design and print A4, A5 or A6.', 'route' => 'dashboard.qr', 'cta' => 'Print QR codes'],
        ];
        $doneCount = collect($steps)->where('done', true)->count();

        $since = now()->subDays(29)->startOfDay();
        $base = Event::where('cafe_id', $cafe->id)->where('created_at', '>=', $since);

        $totals = (clone $base)->selectRaw('type, count(*) as n')->groupBy('type')->pluck('n', 'type');

        $daily = (clone $base)->where('type', 'menu_view')
            ->where('created_at', '>=', now()->subDays(13)->startOfDay())
            ->selectRaw('DATE(created_at) as d, count(*) as n')->groupBy('d')->pluck('n', 'd');
        $chart = collect(range(13, 0))->map(function ($ago) use ($daily) {
            $d = Carbon::today()->subDays($ago);

            return ['label' => $d->format('D j'), 'short' => $d->format('j'), 'n' => (int) ($daily[$d->toDateString()] ?? 0)];
        });

        $topIds = (clone $base)->where('type', 'item_open')->whereNotNull('item_id')
            ->selectRaw('item_id, count(*) as n')->groupBy('item_id')->orderByDesc('n')->limit(5)->pluck('n', 'item_id');
        $topItems = Item::whereIn('id', $topIds->keys())->get()->map(fn ($i) => ['item' => $i, 'n' => $topIds[$i->id]])->sortByDesc('n')->values();

        return view('dashboard.index', compact('cafe', 'steps', 'doneCount', 'totals', 'chart', 'topItems', 'itemCount'));
    }
}
