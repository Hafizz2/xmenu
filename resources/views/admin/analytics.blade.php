@extends('layouts.admin')
@section('title', 'Analytics')

@section('content')
<div class="page-head">
    <div><h1>Platform analytics</h1><p>How customers use menus, and how cafés move from sign-up to paying.</p></div>
    <div class="seg">
        @foreach ([7, 30, 90] as $d)
            <label><input type="radio" onchange="location='?days={{ $d }}'" @checked($days === $d)><span>{{ $d }} days</span></label>
        @endforeach
    </div>
</div>

<div class="kpis">
    <div class="kpi"><div class="l">Menu scans</div><div class="n">{{ number_format($views) }}</div><div class="d">{{ $views ? round($tableScans / $views * 100) : 0 }}% from table QRs</div></div>
    <div class="kpi"><div class="l">Dishes opened</div><div class="n">{{ number_format($totals['item_open'] ?? 0) }}</div><div class="d">{{ $views ? round(($totals['item_open'] ?? 0) / $views, 1) : 0 }} per scan</div></div>
    <div class="kpi"><div class="l">Game open rate</div><div class="n">{{ $gameRate }}%</div><div class="d {{ $gameRate >= 20 ? 'up' : '' }}">Goal: 20%+ · avg play {{ $playTime ? gmdate('i:s', (int) $playTime) : '—' }}</div></div>
    <div class="kpi"><div class="l">Copy taps</div><div class="n">{{ number_format(($totals['pay_copy'] ?? 0) + ($totals['wifi_copy'] ?? 0)) }}</div><div class="d">{{ number_format($totals['pay_copy'] ?? 0) }} pay · {{ number_format($totals['wifi_copy'] ?? 0) }} Wi-Fi</div></div>
</div>

<div class="card card-pad mt-4">
    <div class="section-title">Scans per day</div>
    <div class="mt-3">@include('admin._bars', ['data' => $scansDaily, 'height' => 140])</div>
</div>

<div class="grid-2 mt-4" style="align-items:start">
    <div class="card card-pad">
        <div class="section-title">Activation funnel</div>
        <div class="section-sub">Cafés that signed up in the last {{ $days }} days.</div>
        <div class="funnel mt-2">
            @php $top = max(1, $funnel[0][1]); @endphp
            @foreach ($funnel as [$label, $n])
                <div class="f"><div class="bar"><i style="width:{{ round($n / $top * 100) }}%"></i><span>{{ $label }}</span></div><b>{{ $n }}</b></div>
            @endforeach
        </div>
    </div>
    <div class="card card-pad">
        <div class="section-title">Busy hours</div>
        <div class="section-sub">When customers scan, all cafés combined.</div>
        <div class="mt-3">@include('admin._bars', ['data' => $hourly, 'height' => 140])</div>
    </div>
</div>

<div class="grid-2 mt-4" style="align-items:start">
    <div class="card"><div class="card-head"><h2>Top cafés by scans</h2></div>
        <table class="tbl">@forelse ($topCafes as $i => $r)<tr><td class="muted" style="width:30px">{{ $loop->iteration }}</td><td><a href="{{ route('admin.cafes.show', $r['cafe']) }}">{{ $r['cafe']->name }}</a></td><td style="text-align:right"><b>{{ number_format($r['n']) }}</b></td></tr>@empty<tr><td class="muted">No scans yet.</td></tr>@endforelse</table>
    </div>
    <div class="card"><div class="card-head"><h2>Top games</h2></div>
        <table class="tbl">@forelse ($topGames as $r)<tr><td style="width:56px"><span class="game-thumb" style="width:40px;height:32px;border-radius:8px;background:{{ $r['game']->color }}">{!! $r['game']->artSvg() !!}</span></td><td>{{ tr($r['game']->name, 'en') }}</td><td style="text-align:right"><b>{{ number_format($r['n']) }}</b></td></tr>@empty<tr><td class="muted">No games played yet.</td></tr>@endforelse</table>
    </div>
    <div class="card"><div class="card-head"><h2>What customers search for</h2></div>
        <table class="tbl">@forelse ($topSearches as $term => $n)<tr><td>“{{ $term }}”</td><td style="text-align:right"><b>{{ $n }}</b></td></tr>@empty<tr><td class="muted">No searches yet.</td></tr>@endforelse</table>
    </div>
    <div class="card"><div class="card-head"><h2>Filters used</h2></div>
        <table class="tbl">@forelse ($filters as $tag => $n)<tr><td class="row" style="gap:8px"><x-icon :name="config('xmenu.tags.'.$tag.'.icon', 'info')" class="sm"/>{{ config("xmenu.tags.$tag.en", $tag) }}</td><td style="text-align:right"><b>{{ $n }}</b></td></tr>@empty<tr><td class="muted">No filters used yet.</td></tr>@endforelse</table>
    </div>
</div>

<div class="card card-pad mt-4">
    <div class="section-title">New cafés per day</div>
    <div class="mt-3">@include('admin._bars', ['data' => $signupsDaily, 'height' => 100])</div>
</div>
@endsection
