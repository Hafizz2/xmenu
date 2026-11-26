@php
    $groups = [
        'Menu' => [
            ['dashboard', 'Home', 'home'],
            ['dashboard.menu', 'Menu', 'book'],
            ['dashboard.design', 'Look & colours', 'palette'],
            ['dashboard.qr', 'QR codes', 'qr'],
        ],
        'Café' => [
            ['dashboard.info', 'Café info', 'store'],
            ['dashboard.payments', 'Payments', 'wallet'],
            ['dashboard.games', 'Games', 'game'],
        ],
        'Account' => [
            ['dashboard.billing', 'Plan & billing', 'receipt'],
            ['dashboard.settings', 'Settings', 'settings'],
        ],
    ];
    $nav = collect($groups)->flatten(1)->all();
    $is = fn ($r) => request()->routeIs($r) || ($r !== 'dashboard' && request()->routeIs($r.'.*')) || ($r === 'dashboard.menu' && request()->routeIs('dashboard.items.*', 'dashboard.import'));
    $dock = array_slice($nav, 0, 4);
    $rest = array_slice($nav, 4);
@endphp
<!doctype html>
<html lang="en">
<head>
    @include('partials.head')
    <title>@yield('title', 'Dashboard') · Xmenu</title>
    @stack('head')
</head>
<body class="ambient">
<div class="shell">
    <aside class="sidebar">
        <x-brand style="padding:4px 10px 10px" />
        <nav>
            @foreach ($groups as $group => $links)
                <div class="nav-group">{{ $group }}</div>
                @foreach ($links as [$route, $label, $icon])
                    <a href="{{ route($route) }}" class="nav-link {{ $is($route) ? 'on' : '' }}" style="margin-top:2px"><x-icon :name="$icon"/>{{ $label }}</a>
                @endforeach
            @endforeach
            @if (auth()->user()->is_admin)
                <div class="nav-group">Platform</div>
                <a href="{{ route('admin.index') }}" class="nav-link"><x-icon name="shield"/>Super admin</a>
            @endif
        </nav>
        <div class="side-cafe mt-6">
            <div class="row">
                <div class="avatar">@if ($cafe->logo)<img src="{{ $cafe->logoUrl(128) }}" alt="">@else{{ mb_substr($cafe->name, 0, 1) }}@endif</div>
                <div class="grow"><b class="small" style="display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $cafe->name }}</b>
                    @if ($d = $cafe->trialDaysLeft()) <span class="tiny muted">Trial · {{ $d }} days left</span>@else<span class="tiny muted">{{ ucfirst($cafe->plan) }} plan</span>@endif
                </div>
            </div>
            <a href="{{ $cafe->menuUrl() }}" target="_blank" class="btn btn-sm btn-block mt-3"><x-icon name="external" class="sm"/>Open live menu</a>
            <form method="post" action="{{ route('logout') }}" class="mt-1">@csrf<button class="nav-link small" style="height:34px;justify-content:center;color:var(--muted)"><x-icon name="logout" class="sm"/>Log out</button></form>
        </div>
    </aside>

    <div style="min-width:0">
        <header class="topbar">
            <a href="{{ route('dashboard') }}" class="cafe-mini">
                <div class="avatar">@if ($cafe->logo)<img src="{{ $cafe->logoUrl(128) }}" alt="">@else{{ mb_substr($cafe->name, 0, 1) }}@endif</div>
                <b>{{ $cafe->name }}</b>
            </a>
            <a href="{{ $cafe->menuUrl() }}" target="_blank" class="btn btn-sm"><x-icon name="eye" class="sm"/>Live menu</a>
        </header>
        @if ($managing ?? false)
            <div class="manage-bar">
                <x-icon name="shield" class="sm"/>
                <span class="grow">Admin mode · editing <b>{{ $cafe->name }}</b> <span style="opacity:.6">({{ $cafe->owner->email }})</span>. Changes are logged.</span>
                <form method="post" action="{{ route('admin.manage.stop') }}">@csrf<button class="btn btn-xs">Exit</button></form>
            </div>
        @endif
        <main class="main">
            @if (session('ok'))
                <div class="flash ok" data-autohide><x-icon name="check-circle"/>{{ session('ok') }}</div>
            @endif
            @if (session('error'))
                <div class="flash err"><x-icon name="info"/>{{ session('error') }}</div>
            @endif
            @if ($errors->any() && ! View::hasSection('inline-errors'))
                <div class="flash err"><x-icon name="info"/>{{ $errors->first() }}</div>
            @endif
            @yield('content')
        </main>
    </div>
</div>

<nav class="bottom-nav" aria-label="Main">
    @foreach ($dock as [$route, $label, $icon])
        <a href="{{ route($route) }}" class="{{ $is($route) ? 'on' : '' }}"><x-icon :name="$icon"/>{{ ['Look & colours' => 'Look', 'QR codes' => 'QR'][$label] ?? $label }}</a>
    @endforeach
    <button type="button" data-open-more class="{{ collect($rest)->contains(fn ($n) => $is($n[0])) ? 'on' : '' }}"><x-icon name="more"/>More</button>
</nav>
<div class="more-sheet" id="more-sheet">
    <div class="sheet">
        <div class="grab"></div>
        @foreach ($rest as [$route, $label, $icon])
            <a href="{{ route($route) }}" class="nav-link {{ $is($route) ? 'on' : '' }}"><x-icon :name="$icon"/>{{ $label }}</a>
        @endforeach
        @if (auth()->user()->is_admin)
            <a href="{{ route('admin.index') }}" class="nav-link"><x-icon name="shield"/>Super admin</a>
        @endif
        <form method="post" action="{{ route('logout') }}">@csrf<button class="nav-link"><x-icon name="logout"/>Log out</button></form>
    </div>
</div>

<script src="{{ asset('js/panel.js') }}?v={{ filemtime(public_path('js/panel.js')) }}" defer></script>
@stack('scripts')
</body>
</html>
