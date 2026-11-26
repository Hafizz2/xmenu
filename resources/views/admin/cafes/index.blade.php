@extends('layouts.admin')
@section('title', 'Cafés')

@section('content')
<div class="page-head">
    <div><h1>Cafés</h1><p>Everyone who signed up. Open one to manage its plan or edit its menu for them.</p></div>
</div>

<div class="tabs-line">
    <a href="{{ route('admin.cafes.index', array_filter(['q' => $q, 'plan' => $plan, 'sort' => $sort])) }}" class="{{ ! $status ? 'on' : '' }}">All {{ $counts->sum() }}</a>
    @foreach (\App\Models\Cafe::STATUSES as $s)
        <a href="{{ route('admin.cafes.index', array_filter(['status' => $s, 'q' => $q, 'plan' => $plan, 'sort' => $sort])) }}" class="{{ $status === $s ? 'on' : '' }}">{{ ucfirst($s) }} {{ $counts[$s] ?? 0 }}</a>
    @endforeach
</div>

<form class="filters-row mb-3" method="get" style="margin-bottom:16px">
    @if ($status)<input type="hidden" name="status" value="{{ $status }}">@endif
    <input class="input grow" type="search" name="q" value="{{ $q }}" placeholder="Search name, link, owner email or phone" style="min-width:220px">
    <select class="input" name="plan" onchange="this.form.submit()">
        <option value="">All plans</option>
        @foreach ($plans as $p)<option value="{{ $p->key }}" @selected($plan === $p->key)>{{ $p->name }}</option>@endforeach
    </select>
    <select class="input" name="sort" onchange="this.form.submit()">
        @foreach (['new' => 'Newest first', 'scans' => 'Most scans (30d)', 'expiry' => 'Expiring soonest', 'name' => 'Name A–Z'] as $k => $l)
            <option value="{{ $k }}" @selected($sort === $k)>{{ $l }}</option>
        @endforeach
    </select>
    <button class="btn btn-sm">Search</button>
</form>

<div class="card scroll-x">
    <table class="tbl cards">
        <thead><tr><th>Café</th><th>Owner</th><th>Status</th><th>Plan</th><th>Ends</th><th style="text-align:right">Items</th><th style="text-align:right">Scans 30d</th><th>Joined</th><th></th></tr></thead>
        <tbody>
        @forelse ($cafes as $c)
            @php $end = $c->status === 'trial' ? $c->trial_ends_at : $c->paid_until; @endphp
            <tr>
                <td>
                    <a href="{{ route('admin.cafes.show', $c) }}" class="row" style="text-decoration:none;gap:10px">
                        <span class="avatar" style="width:32px;height:32px">@if ($c->logo)<img src="{{ $c->logoUrl(128) }}" alt="">@else{{ mb_substr($c->name, 0, 1) }}@endif</span>
                        <span><b>{{ $c->name }}</b><br><span class="tiny muted">/{{ $c->slug }}</span></span>
                    </a>
                </td>
                <td><span class="small">{{ $c->owner?->name }}</span><br><span class="tiny muted">{{ $c->owner?->email }}</span></td>
                <td><span class="badge {{ $c->statusTone() }}">{{ $c->status }}</span></td>
                <td class="small">{{ ucfirst($c->plan) }}</td>
                <td class="small {{ $end && $end->isPast() ? '' : 'muted' }}" style="{{ $end && $end->isPast() ? 'color:var(--bad)' : '' }}">{{ $end?->format('j M Y') ?? '—' }}</td>
                <td style="text-align:right">{{ $c->items_count }}</td>
                <td style="text-align:right"><b>{{ number_format($c->scans_30) }}</b></td>
                <td class="small muted">{{ $c->created_at->format('j M Y') }}</td>
                <td style="white-space:nowrap">
                    <a href="{{ route('admin.cafes.show', $c) }}" class="btn btn-xs">Open</a>
                    <form method="post" action="{{ route('admin.cafes.manage', $c) }}" style="display:inline">@csrf<button class="btn btn-xs" title="Edit this café's menu and settings">Manage</button></form>
                </td>
            </tr>
        @empty
            <tr><td colspan="9" class="empty">No cafés match.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $cafes->links('admin._pager') }}</div>
@endsection
