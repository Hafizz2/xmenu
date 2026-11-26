@extends('layouts.admin')
@section('title', 'Users')

@section('content')
<div class="page-head"><div><h1>Users</h1><p>Every login on the platform: café owners and admins.</p></div></div>

<div class="tabs-line">
    @foreach (['' => 'All', 'owner' => 'Café owners', 'admin' => 'Admins', 'inactive' => 'Inactive 30d+', 'suspended' => 'Suspended'] as $k => $l)
        <a href="{{ route('admin.users.index', array_filter(['role' => $k, 'q' => $q])) }}" class="{{ ($role ?? '') === $k ? 'on' : '' }}">{{ $l }} {{ $counts[$k ?: 'all'] }}</a>
    @endforeach
</div>

<form class="filters-row" method="get" style="margin-bottom:16px">
    @if ($role)<input type="hidden" name="role" value="{{ $role }}">@endif
    <input class="input grow" type="search" name="q" value="{{ $q }}" placeholder="Search name, email or phone">
    <button class="btn btn-sm">Search</button>
</form>

<div class="card scroll-x">
    <table class="tbl cards">
        <thead><tr><th>User</th><th>Café</th><th>Role</th><th>Last login</th><th>Joined</th><th></th></tr></thead>
        <tbody>
        @forelse ($users as $u)
            <tr style="{{ $u->isSuspended() ? 'opacity:.6' : '' }}">
                <td><b>{{ $u->name }}</b> @if ($u->id === auth()->id())<span class="badge">You</span>@endif<br><span class="tiny muted">{{ $u->email }}@if ($u->phone) · {{ $u->phone }}@endif</span></td>
                <td>@if ($u->cafe)<a href="{{ route('admin.cafes.show', $u->cafe) }}">{{ $u->cafe->name }}</a><br><span class="badge {{ $u->cafe->statusTone() }}">{{ $u->cafe->status }}</span>@else<span class="muted">—</span>@endif</td>
                <td>@if ($u->is_admin)<span class="badge brand">Admin</span>@else<span class="badge">Owner</span>@endif @if ($u->isSuspended())<span class="badge bad">Suspended</span>@endif</td>
                <td class="small muted">{{ $u->last_login_at?->diffForHumans() ?? 'never' }}</td>
                <td class="small muted">{{ $u->created_at->format('j M Y') }}</td>
                <td style="white-space:nowrap;text-align:right">
                    <button class="btn btn-xs" data-dialog="u-{{ $u->id }}">Edit</button>
                    <div class="dropdown" style="display:inline-block">
                        <button class="btn btn-xs btn-icon" data-dropdown aria-label="More"><x-icon name="dots" class="sm"/></button>
                        <div class="dropdown-menu">
                            @if ($u->cafe)<form method="post" action="{{ route('admin.cafes.manage', $u->cafe) }}">@csrf<button><x-icon name="edit" class="sm"/>Manage their café</button></form>@endif
                            <form method="post" action="{{ route('admin.users.password', $u) }}" data-confirm="Set a new random password for {{ $u->email }}? They will be logged out everywhere.">@csrf<button><x-icon name="settings" class="sm"/>Reset password</button></form>
                            @if ($u->id !== auth()->id())
                                <form method="post" action="{{ route('admin.users.suspend', $u) }}" data-confirm="{{ $u->isSuspended() ? 'Allow this user to log in again?' : 'Block this user from logging in? Their menu stays online.' }}">@csrf<button><x-icon name="shield" class="sm"/>{{ $u->isSuspended() ? 'Unsuspend' : 'Suspend login' }}</button></form>
                                <form method="post" action="{{ route('admin.users.destroy', $u) }}" data-confirm="Delete {{ $u->email }}{{ $u->cafe ? ' AND their café “'.$u->cafe->name.'”' : '' }}? This can’t be undone.">@csrf @method('delete')<button class="danger"><x-icon name="trash" class="sm"/>Delete user</button></form>
                            @endif
                        </div>
                    </div>
                    <dialog id="u-{{ $u->id }}" style="text-align:left">
                        <form method="post" action="{{ route('admin.users.update', $u) }}">
                            @csrf @method('put')
                            <div class="dlg-head"><h3>Edit user</h3><button class="btn btn-ghost btn-icon btn-sm" data-close><x-icon name="x"/></button></div>
                            <div class="dlg-body stack">
                                <div class="field"><label>Name</label><input class="input" name="name" value="{{ $u->name }}" required></div>
                                <div class="field"><label>Email</label><input class="input" type="email" name="email" value="{{ $u->email }}" required></div>
                                <div class="field"><label>Phone</label><input class="input" name="phone" value="{{ $u->phone }}"></div>
                                <label class="switch"><input type="checkbox" name="is_admin" value="1" @checked($u->is_admin)><span class="track"></span><span><b>Super admin</b><br><span class="tiny muted">Full access to this admin panel.</span></span></label>
                            </div>
                            <div class="dlg-foot"><button type="button" class="btn" data-close>Cancel</button><button class="btn btn-primary">Save</button></div>
                        </form>
                    </dialog>
                </td>
            </tr>
        @empty
            <tr><td colspan="6" class="empty">No users match.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $users->links('admin._pager') }}</div>
@endsection
