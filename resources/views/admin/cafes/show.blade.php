@extends('layouts.admin')
@section('title', $cafe->name)

@section('content')
<div class="page-head">
    <div>
        <a href="{{ route('admin.cafes.index') }}" class="small muted row" style="gap:4px;text-decoration:none"><x-icon name="left" class="sm"/>Cafés</a>
        <div class="row mt-1" style="gap:12px">
            <span class="avatar" style="width:48px;height:48px;border-radius:14px;font-size:20px">@if ($cafe->logo)<img src="{{ $cafe->logoUrl(128) }}" alt="">@else{{ mb_substr($cafe->name, 0, 1) }}@endif</span>
            <div><h1>{{ $cafe->name }}</h1><p class="small" style="margin:2px 0 0"><span class="badge {{ $cafe->statusTone() }}">{{ $cafe->status }}</span> · {{ ucfirst($cafe->plan) }} plan · joined {{ $cafe->created_at->format('j M Y') }}</p></div>
        </div>
    </div>
    <div class="row row-wrap">
        <a href="{{ $cafe->menuUrl() }}" target="_blank" class="btn"><x-icon name="eye"/>View menu</a>
        <form method="post" action="{{ route('admin.cafes.manage', $cafe) }}">@csrf<button class="btn btn-primary"><x-icon name="edit"/>Manage content</button></form>
    </div>
</div>

<div class="stats">
    <div class="stat"><div class="l">Scans · 30d</div><div class="n">{{ number_format($totals['menu_view'] ?? 0) }}</div></div>
    <div class="stat"><div class="l">Dishes opened · 30d</div><div class="n">{{ number_format($totals['item_open'] ?? 0) }}</div></div>
    <div class="stat"><div class="l">Games · 30d</div><div class="n">{{ number_format($totals['game_open'] ?? 0) }}</div></div>
    <div class="stat"><div class="l">Revenue from café</div><div class="n">{{ number_format($revenue) }}</div></div>
</div>

<div class="card card-pad mt-4">
    <div class="row between"><div class="section-title">Scans, 30 days</div><span class="tiny muted">Last scan: {{ $lastScan ? \Illuminate\Support\Carbon::parse($lastScan)->diffForHumans() : 'never' }}</span></div>
    <div class="mt-3">@include('admin._bars', ['data' => $daily, 'height' => 110])</div>
</div>

<div class="grid-2 mt-4" style="align-items:start">
    <div class="stack">
        <div class="card card-pad">
            <div class="section-title">Owner</div>
            <dl class="dl mt-3">
                <dt>Name</dt><dd>{{ $cafe->owner?->name }}</dd>
                <dt>Email</dt><dd><a href="mailto:{{ $cafe->owner?->email }}">{{ $cafe->owner?->email }}</a></dd>
                <dt>Phone</dt><dd>{{ $cafe->owner?->phone ?: $cafe->phone ?: '—' }}</dd>
                <dt>Last login</dt><dd>{{ $cafe->owner?->last_login_at?->diffForHumans() ?? 'never' }}</dd>
                <dt>Account</dt><dd>@if ($cafe->owner?->isSuspended())<span class="badge bad">Suspended</span>@else<span class="badge ok">Can log in</span>@endif
                    <a href="{{ route('admin.users.index', ['q' => $cafe->owner?->email]) }}" class="small">Manage user →</a></dd>
            </dl>
        </div>
        <div class="card card-pad">
            <div class="section-title">Menu</div>
            <dl class="dl mt-3">
                <dt>Link</dt><dd><a href="{{ $cafe->menuUrl() }}" target="_blank">/{{ $cafe->slug }}</a></dd>
                <dt>Content</dt><dd>{{ $cafe->categories_count }} categories · {{ $cafe->items_count }} items · {{ $cafe->payment_accounts_count }} payment accounts</dd>
                <dt>Logo / cover</dt><dd>{{ $cafe->logo ? 'Yes' : 'No' }} / {{ $cafe->cover ? 'Yes' : 'No' }}</dd>
                <dt>Design</dt><dd>{{ ! empty($cafe->theme['_saved']) ? 'Customised' : 'Default' }} · <span style="display:inline-block;width:12px;height:12px;border-radius:50%;background:{{ $cafe->themeValue('color') }};vertical-align:-1px"></span> {{ $cafe->themeValue('layout') }}</dd>
                <dt>QR downloaded</dt><dd>{{ ! empty($cafe->qr_settings['downloaded_at']) ? \Illuminate\Support\Carbon::parse($cafe->qr_settings['downloaded_at'])->format('j M Y') : 'Not yet' }}</dd>
            </dl>
        </div>
    </div>

    <div class="stack">
        <form method="post" action="{{ route('admin.cafes.update', $cafe) }}" class="card card-pad">
            @csrf @method('put')
            <div class="section-title">Subscription</div>
            <div class="grid-2 mt-3">
                <div class="field"><label>Status</label>
                    <select class="input" name="status">@foreach (\App\Models\Cafe::STATUSES as $s)<option value="{{ $s }}" @selected($cafe->status === $s)>{{ ucfirst($s) }}</option>@endforeach</select></div>
                <div class="field"><label>Plan</label>
                    <select class="input" name="plan">@foreach ($plans as $p)<option value="{{ $p->key }}" @selected($cafe->plan === $p->key)>{{ $p->name }}</option>@endforeach</select></div>
                <div class="field"><label>Trial ends</label><input class="input" type="date" name="trial_ends_at" value="{{ $cafe->trial_ends_at?->format('Y-m-d') }}"></div>
                <div class="field"><label>Paid until</label><input class="input" type="date" name="paid_until" value="{{ $cafe->paid_until?->format('Y-m-d') }}"></div>
            </div>
            <div class="field mt-3"><label>Private notes</label><textarea class="input" name="admin_notes" rows="3" placeholder="Only admins see this. E.g. called on 12 Nov, wants Pro after holidays.">{{ $cafe->admin_notes }}</textarea></div>
            <button class="btn btn-primary mt-3">Save</button>
        </form>

        <div class="card card-pad">
            <div class="section-title">Quick actions</div>
            <div class="row row-wrap mt-3">
                <form method="post" action="{{ route('admin.cafes.extend', $cafe) }}" class="row">@csrf
                    <input class="input" type="number" name="days" value="14" min="1" max="365" style="width:80px;height:36px"><button class="btn btn-sm">Extend trial (days)</button>
                </form>
                <form method="post" action="{{ route('admin.cafes.suspend', $cafe) }}" data-confirm="{{ $cafe->status === 'suspended' ? 'Make this menu live again?' : 'Suspend this menu? Customers will see “unavailable”.' }}">@csrf
                    <button class="btn btn-sm {{ $cafe->status === 'suspended' ? '' : 'btn-danger' }}">{{ $cafe->status === 'suspended' ? 'Unsuspend menu' : 'Suspend menu' }}</button>
                </form>
                <a href="{{ route('admin.sales.index', ['cafe' => $cafe->id]) }}#record" class="btn btn-sm">Record payment</a>
            </div>
        </div>
    </div>
</div>

<div class="grid-2 mt-4" style="align-items:start">
    <div class="card">
        <div class="card-head"><h2>Payments</h2></div>
        <ul class="list">
            @forelse ($cafe->subscriptions as $s)
                <li style="padding:12px 20px">
                    <div class="grow"><b class="small">{{ number_format($s->amount) }} {{ $s->currency }}</b> <span class="tiny muted">· {{ ucfirst($s->plan) }} × {{ $s->months }} mo · {{ \App\Models\Subscription::METHODS[$s->method] ?? $s->method }}</span><br>
                        <span class="tiny muted">{{ $s->created_at->format('j M Y') }}@if ($s->reference) · Ref {{ $s->reference }}@endif @if ($s->period_end) · until {{ $s->period_end->format('j M Y') }}@endif</span></div>
                    <span class="badge {{ ['approved' => 'ok', 'pending' => 'warn', 'rejected' => 'bad'][$s->status] }}">{{ $s->status }}</span>
                </li>
            @empty
                <li class="small muted">No payments yet.</li>
            @endforelse
        </ul>
    </div>
    <div class="card">
        <div class="card-head"><h2>Admin activity</h2><a href="{{ route('admin.logs', ['cafe' => $cafe->id]) }}" class="small">All →</a></div>
        <ul class="list">
            @forelse ($logs as $l)
                <li style="padding:10px 20px"><div class="grow"><b class="small">{{ $l->action }}</b><br><span class="tiny muted">{{ $l->user?->name ?? 'System' }} · {{ $l->created_at->diffForHumans() }}</span></div></li>
            @empty
                <li class="small muted">No admin actions on this café.</li>
            @endforelse
        </ul>
    </div>
</div>

<form method="post" action="{{ route('admin.cafes.destroy', $cafe) }}" class="card card-pad mt-4" style="border-color:#F4C7C7">
    @csrf @method('delete')
    <div class="section-title" style="color:var(--bad)">Delete café</div>
    <p class="small muted mt-1">Removes the menu, photos, stats and payments. The owner’s login stays. This can’t be undone. Type <code>{{ $cafe->slug }}</code> to confirm.</p>
    <div class="row mt-3"><input class="input" name="confirm" placeholder="{{ $cafe->slug }}" style="max-width:260px"><button class="btn btn-danger">Delete permanently</button></div>
</form>
@endsection
