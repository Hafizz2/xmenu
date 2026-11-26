<!doctype html>
<html lang="en">
<head>
    @include('partials.head')
    <title>@yield('title') · Xmenu</title>
</head>
<body class="ambient">
<div class="auth">
    <div class="auth-form">
        <x-brand />
        @yield('content')
    </div>
    <div class="auth-art">
        <span class="orb" style="width:420px;height:420px;background:#F0562A;top:-120px;right:-80px"></span>
        <span class="orb" style="width:380px;height:380px;background:#6D5BFF;bottom:-140px;left:-100px;opacity:.5"></span>
        <div style="position:relative;display:flex;gap:40px;align-items:center;max-width:620px">
            <div style="flex:1;color:#fff">
                <span class="badge plain" style="background:rgba(255,255,255,.1);color:#E5E5EA;border:1px solid rgba(255,255,255,.12);height:28px;padding:0 12px">Live in under 30 minutes</span>
                <h2 style="font-size:38px;line-height:1.05;margin-top:22px;letter-spacing:-.04em;font-weight:700">Your menu, on every table. Always up to date.</h2>
                <ul style="list-style:none;padding:0;margin:28px 0 0;color:#B9B9C2;font-size:15px">
                    @foreach (['Change prices from your phone in seconds', 'Photos, fasting filter and Wi-Fi', 'Print QR codes in A4, A5 or A6', 'Wi-Fi and payment details in one tap'] as $f)
                        <li style="display:flex;gap:12px;align-items:center;margin-top:12px"><x-icon name="check-circle" class="sm" style="color:#FF8A5B"/>{{ $f }}</li>
                    @endforeach
                </ul>
            </div>
            @if (file_exists(public_path('img/screens/menu-mobile.png')))
                <div style="width:220px;flex:none;border-radius:34px;padding:8px;background:linear-gradient(160deg,#3A3A42,#111);box-shadow:0 40px 80px -20px rgba(0,0,0,.6),inset 0 0 0 1px rgba(255,255,255,.12)">
                    <img src="{{ asset('img/screens/menu-mobile.png') }}" alt="A real Xmenu customer menu" style="border-radius:27px;width:100%;aspect-ratio:9/19;object-fit:cover;object-position:top">
                </div>
            @endif
        </div>
    </div>
</div>
</body>
</html>
