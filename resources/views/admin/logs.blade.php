@extends('layouts.admin')
@section('title', 'Audit log')

@section('content')
<div class="page-head"><div><h1>Audit log</h1><p>Every admin action, including every edit made in a café’s dashboard on its behalf.</p></div></div>

<div class="tabs-line">
    <a href="{{ route('admin.logs') }}" class="{{ ! $action ? 'on' : '' }}">All</a>
    @foreach ($groups as $g)<a href="{{ route('admin.logs', ['action' => $g]) }}" class="{{ $action === $g ? 'on' : '' }}">{{ ucfirst($g) }}</a>@endforeach
</div>

<div class="card scroll-x">
    <table class="tbl cards">
        <thead><tr><th>When</th><th>Admin</th><th>Action</th><th>Café</th><th>Details</th><th>IP</th></tr></thead>
        <tbody>
        @forelse ($logs as $l)
            <tr>
                <td class="small muted" style="white-space:nowrap">{{ $l->created_at->format('j M Y H:i') }}</td>
                <td class="small">{{ $l->user?->name ?? 'System' }}</td>
                <td><span class="badge">{{ $l->action }}</span></td>
                <td class="small">@if ($l->cafe)<a href="{{ route('admin.cafes.show', $l->cafe) }}">{{ $l->cafe->name }}</a>@elseif ($l->cafe_id)<span class="muted">#{{ $l->cafe_id }} (deleted)</span>@else<span class="muted">—</span>@endif</td>
                <td class="tiny muted mono" style="max-width:380px;word-break:break-all">{{ $l->details ? \Illuminate\Support\Str::limit(json_encode($l->details, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), 220) : '' }}</td>
                <td class="tiny muted">{{ $l->ip }}</td>
            </tr>
        @empty
            <tr><td colspan="6" class="empty">No activity yet.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $logs->links('admin._pager') }}</div>
@endsection
