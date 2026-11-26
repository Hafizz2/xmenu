@extends('layouts.panel')
@section('title', 'Overview')

@section('content')
<div class="page-head">
    <div>
        <h1>{{ session('welcome') ? 'Welcome to Xmenu' : 'Good '.(now()->hour < 12 ? 'morning' : (now()->hour < 18 ? 'afternoon' : 'evening')).', '.explode(' ', $cafe->owner->name)[0] }}</h1>
        <p>{{ $doneCount < count($steps) ? 'A few steps and your café is live on every table.' : 'Your menu is live. Here’s how it’s doing.' }}</p>
    </div>
</div>

<div class="quick">
    @foreach ([
        ['dashboard.menu', 'edit', 'Change prices or mark sold out', 'Your whole menu on one page'],
        ['dashboard.items.create', 'plus', 'Add a dish', 'Name, price and a photo'],
        ['dashboard.qr', 'qr', 'Print QR codes', 'For tables, the door and the counter'],
        ['dashboard.info', 'wifi', 'Wi-Fi, hours and contact', 'What customers see under Info'],
        ['dashboard.design', 'palette', 'Change how it looks', 'Colours, layout and cover photo'],
    ] as [$r, $ic, $t, $sub])
        <a href="{{ route($r) }}" class="qa"><span class="qa-ic"><x-icon :name="$ic"/></span><span><b>{{ $t }}</b><small>{{ $sub }}</small></span><x-icon name="right" class="sm qa-go"/></a>
    @endforeach
    <a href="{{ $cafe->menuUrl() }}" target="_blank" class="qa"><span class="qa-ic"><x-icon name="eye"/></span><span><b>See my menu</b><small>Exactly what customers see</small></span><x-icon name="external" class="sm qa-go"/></a>
</div>

<div class="card card-pad mt-4">
    <div class="row between row-wrap" style="align-items:flex-start">
        <div>
            <div class="section-title"><x-icon name="link" class="sm"/>Your menu link</div>
            <div class="section-sub">Your QR codes open this link. You can also share it on Telegram or Instagram.</div>
        </div>
        <span class="badge {{ $cafe->statusTone() }}">{{ $cafe->status }}</span>
    </div>
    <div class="link-box mt-4">
        <code>{{ $cafe->menuUrl() }}</code>
        <button type="button" class="btn btn-sm" data-copy="{{ $cafe->menuUrl() }}"><x-icon name="copy" class="sm"/>Copy</button>
        <a href="{{ $cafe->menuUrl() }}" target="_blank" class="btn btn-sm btn-primary"><x-icon name="external" class="sm"/>Open</a>
    </div>
</div>

@if ($doneCount < count($steps))
<div class="card mt-4">
    <div class="card-pad" style="padding-bottom:10px">
        <div class="row between">
            <div class="section-title">Get your café live</div>
            <span class="small muted">{{ $doneCount }} of {{ count($steps) }}</span>
        </div>
        <div class="progress mt-3"><span style="width:{{ max(4, round($doneCount / count($steps) * 100)) }}%"></span></div>
        <ol class="steps mt-3">
            @foreach ($steps as $n => $step)
                <li class="step {{ $step['done'] ? 'done' : '' }}">
                    <span class="dot">@if ($step['done'])<x-icon name="check" class="sm" style="width:14px;height:14px"/>@else{{ $n + 1 }}@endif</span>
                    <div class="grow">
                        <h4>{{ $step['title'] }}</h4>
                        <p class="small muted">{{ $step['text'] }}</p>
                    </div>
                    @unless ($step['done'])
                        <a href="{{ route($step['route']) }}" class="btn btn-sm">{{ $step['cta'] }}<x-icon name="right" class="sm"/></a>
                    @endunless
                </li>
            @endforeach
        </ol>
    </div>
</div>
@endif

<div class="section-title mt-8" style="margin-bottom:12px">Last 30 days</div>
<div class="stats">
    <div class="stat"><div class="l"><x-icon name="qr"/>Menu opened</div><div class="n">{{ number_format($totals['menu_view'] ?? 0) }}</div></div>
    <div class="stat"><div class="l"><x-icon name="eye"/>Dishes looked at</div><div class="n">{{ number_format($totals['item_open'] ?? 0) }}</div></div>
    <div class="stat"><div class="l"><x-icon name="game"/>Games played</div><div class="n">{{ number_format($totals['game_open'] ?? 0) }}</div></div>
    <div class="stat"><div class="l"><x-icon name="book"/>Dishes on menu</div><div class="n">{{ $itemCount }}</div></div>
</div>

<div class="grid-2 mt-4" style="align-items:start">
    <div class="card card-pad">
        <div class="row between"><div class="section-title">Menu opened per day</div><span class="tiny muted">Last 14 days</span></div>
        @php $max = max(1, $chart->max('n')); @endphp
        <div class="bars mt-4" role="img" aria-label="Daily menu scans">
            @foreach ($chart as $d)
                <div class="b {{ $d['n'] ? '' : 'zero' }}" title="{{ $d['label'] }}: {{ $d['n'] }} scans">
                    <i style="height:{{ max(2, round($d['n'] / $max * 100)) }}%"></i>
                    <span>{{ $d['short'] }}</span>
                </div>
            @endforeach
        </div>
    </div>
    <div class="card">
        <div class="card-head"><h2>Dishes people look at most</h2></div>
        @if ($topItems->isEmpty())
            <div class="empty" style="padding:32px 20px"><div class="glyph"><x-icon name="trend"/></div><p class="small">When customers open dishes, your favourites show up here.</p></div>
        @else
            <ul class="list">
                @foreach ($topItems as $i => $row)
                    <li style="padding:12px 22px"><span class="faint mono small" style="width:18px">{{ $i + 1 }}</span><span class="grow">{{ tr($row['item']->name) }}</span><b class="mono">{{ $row['n'] }}</b></li>
                @endforeach
            </ul>
        @endif
    </div>
</div>
@endsection
