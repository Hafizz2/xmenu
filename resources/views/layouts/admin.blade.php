@php
    $pendingPayments = \App\Models\Subscription::where('status', 'pending')->count();
    $groups = [
        'Business' => [
            ['admin.index', 'Overview', 'chart', null],
            ['admin.sales.index', 'Sales', 'wallet', $pendingPayments ?: null],
            ['admin.plans', 'Plans & pricing', 'receipt', null],
            ['admin.analytics', 'Analytics', 'trend', null],
        ],
        'Customers' => [
            ['admin.cafes.index', 'Cafés', 'store', null],
            ['admin.users.index', 'Users', 'users', null],
        ],
        'Platform' => [
            ['admin.games', 'Game library', 'game', null],
            ['admin.logs', 'Audit log', 'shield', null],
            ['admin.settings', 'Settings', 'settings', null],
        ],
    ];
    $is = fn ($r) => request()->routeIs($r) || request()->routeIs(preg_replace('/\.index$/', '', $r).'.*');
@endphp
<!doctype html>
<html lang="en">
<head>
    @include('partials.head')
    <meta name="robots" content="noindex">
    <title>@yield('title', 'Admin') · Xmenu Admin</title>
</head>
<body class="admin ambient">
<div class="shell">
    <aside class="sidebar">
        <x-brand :href="route('admin.index')" tag="ADMIN" style="padding:4px 10px 10px" />
        <nav>
            @foreach ($groups as $group => $links)
                <div class="nav-group">{{ $group }}</div>
                @foreach ($links as [$route, $label, $icon, $badge])
                    <a href="{{ route($route) }}" class="nav-link {{ $is($route) ? 'on' : '' }}" style="margin-top:2px"><x-icon :name="$icon"/><span class="grow">{{ $label }}</span>@if ($badge)<span class="badge warn plain">{{ $badge }}</span>@endif</a>
                @endforeach
            @endforeach
        </nav>
        <div style="margin-top:auto;padding-top:16px">
            @if (auth()->user()->cafe)
                <a href="{{ route('dashboard') }}" class="nav-link"><x-icon name="home"/>My café</a>
            @endif
            <form method="post" action="{{ route('logout') }}">@csrf<button class="nav-link"><x-icon name="logout"/>Log out</button></form>
            <p class="tiny" style="color:#6E6E78;padding:8px 12px 0;overflow:hidden;text-overflow:ellipsis">{{ auth()->user()->email }}</p>
        </div>
    </aside>

    <div style="min-width:0">
        <header class="topbar">
            <x-brand :href="route('admin.index')" tag="ADMIN" style="font-size:17px" />
            <span class="grow"></span>
            @if ($pendingPayments)<a href="{{ route('admin.sales.index') }}" class="btn btn-sm"><x-icon name="wallet" class="sm"/>{{ $pendingPayments }}</a>@endif
            <button type="button" class="btn btn-sm btn-icon" data-open-more aria-label="Menu"><x-icon name="menu" class="sm"/></button>
        </header>
        <main class="main" style="max-width:1200px">
            @if (session('ok'))<div class="flash ok"><x-icon name="check-circle"/>{{ session('ok') }}</div>@endif
            @if (session('error'))<div class="flash err"><x-icon name="info"/>{{ session('error') }}</div>@endif
            @if ($errors->any())<div class="flash err"><x-icon name="info"/>{{ $errors->first() }}</div>@endif
            @yield('content')
        </main>
    </div>
</div>
<div class="more-sheet" id="more-sheet">
    <div class="sheet" style="max-height:80vh;overflow:auto">
        <div class="grab"></div>
        @foreach ($groups as $group => $links)
            @foreach ($links as [$route, $label, $icon, $badge])
                <a href="{{ route($route) }}" class="nav-link {{ $is($route) ? 'on' : '' }}"><x-icon :name="$icon"/><span class="grow">{{ $label }}</span>@if ($badge)<span class="badge warn plain">{{ $badge }}</span>@endif</a>
            @endforeach
        @endforeach
        <form method="post" action="{{ route('logout') }}">@csrf<button class="nav-link"><x-icon name="logout"/>Log out</button></form>
    </div>
</div>
<script src="{{ asset('js/panel.js') }}?v={{ filemtime(public_path('js/panel.js')) }}" defer></script>
@stack('scripts')
</body>
</html>
