@extends('layouts.admin')
@section('title', 'Overview')

@php
    $cur = \App\Models\Plan::value('currency') ?? 'ETB';
    $pct = fn ($a, $b) => $b > 0 ? round(($a - $b) / $b * 100) : null;
@endphp

@section('content')
<div class="page-head">
    <div><h1>Overview</h1><p>Your Xmenu business at a glance · {{ now()->format('l, j F Y') }}</p></div>
    <div class="row">
        <a href="{{ route('admin.sales.index') }}" class="btn">Record payment</a>
        <a href="{{ route('admin.cafes.index') }}" class="btn btn-primary"><x-icon name="store"/>All cafés</a>
    </div>
</div>

@php
    $todo = collect();
    foreach ($pending as $s) {
        $todo->push(['tone' => 'warn', 'icon' => 'wallet', 'title' => 'Approve payment from '.($s->cafe?->name ?? 'a café'), 'sub' => number_format($s->amount).' '.$s->currency.' · '.ucfirst($s->plan).' · '.$s->months.' month'.($s->months > 1 ? 's' : '').' · '.$s->created_at->diffForHumans(), 'url' => route('admin.sales.index'), 'cta' => 'Review']);
    }
    foreach ($overdue as $c) {
        $todo->push(['tone' => 'bad', 'icon' => 'clock', 'title' => $c->name.' has not paid', 'sub' => 'Plan ended '.($c->paid_until?->diffForHumans() ?? 'recently').($c->phone ? ' · '.$c->phone : ''), 'url' => route('admin.cafes.show', $c), 'cta' => 'Open', 'tel' => $c->phone]);
    }
    foreach ($expiring as $c) {
        $end = $c->status === 'trial' ? $c->trial_ends_at : $c->paid_until;
        $todo->push(['tone' => 'brand', 'icon' => 'calendar', 'title' => $c->name.($c->status === 'trial' ? ': free trial ends ' : ': plan ends ').$end?->diffForHumans(), 'sub' => 'A quick call now helps them stay'.($c->phone ? ' · '.$c->phone : ''), 'url' => route('admin.cafes.show', $c), 'cta' => 'Open', 'tel' => $c->phone]);
    }
    foreach ($stuck as $c) {
        $todo->push(['tone' => 'info', 'icon' => 'help', 'title' => $c->name.' has no dishes yet', 'sub' => 'Signed up '.$c->created_at->diffForHumans().'. Offer to add their menu for them.', 'url' => route('admin.cafes.show', $c), 'cta' => 'Help', 'tel' => $c->phone]);
    }
@endphp

<div class="card todo">
    <div class="card-head"><h2>Needs your attention</h2><span class="small muted">{{ $todo->count() ? $todo->count().' to do' : '' }}</span></div>
    @if ($todo->isEmpty())
        <div class="todo-done"><span class="qa-ic" style="background:var(--ok-soft);color:var(--ok)"><x-icon name="check"/></span><div><b>All caught up</b><small>No payments to approve and nobody close to expiring.</small></div></div>
    @else
        <ul class="list">
            @foreach ($todo as $t)
                <li>
                    <span class="todo-ic {{ $t['tone'] }}"><x-icon :name="$t['icon']"/></span>
                    <a href="{{ $t['url'] }}" class="grow" style="text-decoration:none;min-width:0"><b style="display:block">{{ $t['title'] }}</b><span class="small muted">{{ $t['sub'] }}</span></a>
                    @if (! empty($t['tel']))<a href="tel:{{ preg_replace('/[^\d+]/', '', $t['tel']) }}" class="btn btn-sm btn-icon" aria-label="Call"><x-icon name="phone" class="sm"/></a>@endif
                    <a href="{{ $t['url'] }}" class="btn btn-sm">{{ $t['cta'] }}</a>
                </li>
            @endforeach
        </ul>
    @endif
</div>

<div class="kpis mt-4">
    <div class="kpi"><div class="l">Monthly income (MRR)</div><div class="n">{{ number_format($mrr) }} <small class="small muted">{{ $cur }}</small></div><div class="d">{{ $statusCounts['active'] ?? 0 }} paying cafés</div></div>
    @php $rp = $pct($revThisMonth, $revLastMonth); @endphp
    <div class="kpi"><div class="l">Revenue this month</div><div class="n">{{ number_format($revThisMonth) }}</div><div class="d {{ $rp === null ? '' : ($rp >= 0 ? 'up' : 'down') }}">{{ $rp === null ? 'Last month: '.number_format($revLastMonth) : ($rp >= 0 ? '+' : '-').abs($rp).'% vs last month' }}</div></div>
    @php $sp = $pct($newThisMonth, $newLastMonth); @endphp
    <div class="kpi"><div class="l">Sign-ups this month</div><div class="n">{{ $newThisMonth }}</div><div class="d {{ $sp === null ? '' : ($sp >= 0 ? 'up' : 'down') }}">{{ $sp === null ? 'Last month: '.$newLastMonth : ($sp >= 0 ? '+' : '-').abs($sp).'% vs last month' }}</div></div>
    <div class="kpi"><div class="l">Menu scans · 30d</div><div class="n">{{ number_format($scans30) }}</div><div class="d">{{ $liveCafes }} cafés scanned this week</div></div>
</div>

<div class="kpis mt-3">
    @foreach (['trial' => 'On trial', 'active' => 'Active (paid)', 'overdue' => 'Overdue', 'suspended' => 'Suspended'] as $k => $l)
        <a href="{{ route('admin.cafes.index', ['status' => $k]) }}" class="kpi" style="text-decoration:none"><div class="l">{{ $l }}</div><div class="n">{{ $statusCounts[$k] ?? 0 }}</div><div class="d">of {{ $totalCafes }} cafés</div></a>
    @endforeach
</div>

<div class="grid-2 mt-4" style="align-items:start">
    <div class="card card-pad">
        <div class="row between"><div class="section-title">Revenue by month</div><span class="tiny muted">All time: {{ number_format($revTotal) }} {{ $cur }}</span></div>
        <div class="mt-3">@include('admin._bars', ['data' => $revenue, 'money' => true, 'label' => 'Monthly revenue'])</div>
    </div>
    <div class="card card-pad">
        <div class="row between"><div class="section-title">New cafés, 30 days</div><span class="tiny muted">{{ $signups->sum('n') }} total</span></div>
        <div class="mt-3">@include('admin._bars', ['data' => $signups, 'label' => 'Daily sign-ups'])</div>
    </div>
</div>

<div class="card card-pad mt-4">
    <div class="row between"><div class="section-title">Menu scans across all cafés, 30 days</div><span class="tiny muted">{{ number_format($games30) }} games played</span></div>
    <div class="mt-3">@include('admin._bars', ['data' => $scans, 'label' => 'Daily scans', 'height' => 120])</div>
</div>

<div class="mt-4">
    <div class="card">
        <div class="card-head"><h2>Latest sign-ups</h2><a href="{{ route('admin.cafes.index') }}" class="small">All →</a></div>
        <ul class="list">
            @forelse ($recent as $c)
                <li style="padding:12px 20px">
                    <div class="avatar" style="width:32px;height:32px">@if ($c->logo)<img src="{{ $c->logoUrl(128) }}" alt="">@else{{ mb_substr($c->name, 0, 1) }}@endif</div>
                    <a href="{{ route('admin.cafes.show', $c) }}" class="grow" style="text-decoration:none;min-width:0">
                        <b class="small" style="display:block">{{ $c->name }}</b>
                        <span class="tiny muted">{{ $c->owner?->email }} · {{ $c->items_count }} items · {{ $c->created_at->diffForHumans() }}</span>
                    </a>
                    <span class="badge {{ $c->statusTone() }}">{{ $c->status }}</span>
                </li>
            @empty
                <li class="muted small">No cafés yet.</li>
            @endforelse
        </ul>
    </div>
</div>
@endsection
