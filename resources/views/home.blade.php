@php
    $s = fn ($f) => asset('img/screens/'.$f).'?v='.(file_exists(public_path('img/screens/'.$f)) ? filemtime(public_path('img/screens/'.$f)) : 0);
    $support = \App\Models\Setting::get('support', []);
    $title = 'Xmenu · QR menus for cafés and restaurants in Ethiopia';
    $desc = 'Create a beautiful QR menu for your café in minutes. Photos, fasting filter, Wi-Fi and Telebirr details, games for customers. Print QR codes in A4, A5 or A6. Free for '.$trialDays.' days.';
@endphp
<!doctype html>
<html lang="en">
<head>
@include('partials.head')
<title>{{ $title }}</title>
<meta name="description" content="{{ $desc }}">
<link rel="canonical" href="{{ url('/') }}">
<meta property="og:type" content="website">
<meta property="og:site_name" content="Xmenu">
<meta property="og:title" content="{{ $title }}">
<meta property="og:description" content="{{ $desc }}">
<meta property="og:url" content="{{ url('/') }}">
<meta property="og:image" content="{{ $s('menu-desktop.webp') }}">
<meta name="twitter:card" content="summary_large_image">
<script type="application/ld+json">{!! json_encode([
    '@context' => 'https://schema.org', '@type' => 'SoftwareApplication', 'name' => 'Xmenu', 'applicationCategory' => 'BusinessApplication',
    'operatingSystem' => 'Web', 'url' => url('/'), 'description' => $desc,
    'publisher' => ['@type' => 'Organization', 'name' => 'Xvalue', 'url' => 'https://xvalue.et'],
    'offers' => ['@type' => 'Offer', 'price' => (string) ($plans->min('price') ?? 0), 'priceCurrency' => 'ETB'],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
<style>
body{background:#FBFBFD}
.wrap{max-width:1180px;margin:0 auto;padding:0 20px}
/* glass nav */
.nav{position:sticky;top:12px;z-index:50;margin:12px auto 0;max-width:1180px;padding:0 12px}
.nav-in{display:flex;align-items:center;gap:18px;height:60px;padding:0 10px 0 18px;border-radius:20px;background:rgba(255,255,255,.7);-webkit-backdrop-filter:saturate(180%) blur(20px);backdrop-filter:saturate(180%) blur(20px);border:1px solid rgba(255,255,255,.8);box-shadow:var(--glass-shadow)}
.nav .links{display:none;gap:4px;margin-left:12px}
.nav .links a{padding:8px 12px;border-radius:10px;text-decoration:none;font-size:14px;font-weight:520;color:var(--ink-2)}
.nav .links a:hover{background:rgba(0,0,0,.05)}
@media(min-width:900px){.nav .links{display:flex}}
.nav .login{font-size:14px;font-weight:550;text-decoration:none;padding:8px 10px;display:none}
@media(min-width:500px){.nav .login{display:inline}}
/* hero */
.hero{position:relative;padding:72px 0 40px;text-align:center;overflow:hidden}
.hero::before{content:"";position:absolute;inset:-20% -10% auto;height:900px;background:radial-gradient(600px 380px at 25% 30%,rgba(255,160,120,.35),transparent 70%),radial-gradient(600px 400px at 78% 20%,rgba(150,160,255,.3),transparent 70%),radial-gradient(500px 360px at 50% 70%,rgba(120,220,190,.22),transparent 70%);z-index:-1;filter:blur(10px)}
.hero h1{font-size:clamp(42px,7.4vw,86px);line-height:.98;letter-spacing:-.055em;font-weight:760;margin:0 auto;max-width:960px}
.hero h1 .soft{background:linear-gradient(100deg,#F0562A 10%,#C2410C 45%,#7C3AED 100%);-webkit-background-clip:text;background-clip:text;color:transparent}
.hero .lead{font-size:clamp(17px,2vw,20px);color:var(--ink-2);max-width:640px;margin:22px auto 0;line-height:1.55}
.cta{display:flex;gap:12px;justify-content:center;flex-wrap:wrap;margin-top:32px}
.trust{display:flex;gap:22px;justify-content:center;flex-wrap:wrap;margin-top:22px;color:var(--muted);font-size:13.5px}
.trust span{display:flex;align-items:center;gap:6px}
.trust svg{color:var(--ok)}
/* device frames */
.stage{position:relative;max-width:1100px;margin:56px auto 0;padding-bottom:40px}
.browser{border-radius:18px;overflow:hidden;background:#fff;box-shadow:0 50px 100px -30px rgba(20,20,40,.35),0 0 0 1px rgba(0,0,0,.06)}
.browser .bar{display:flex;align-items:center;gap:7px;height:38px;padding:0 14px;background:rgba(246,246,248,.9);border-bottom:1px solid rgba(0,0,0,.06)}
.browser .bar i{width:11px;height:11px;border-radius:50%;background:#E3E3E8}
.browser .bar i:nth-child(1){background:#FF5F57}.browser .bar i:nth-child(2){background:#FEBC2E}.browser .bar i:nth-child(3){background:#28C840}
.browser .bar span{flex:1;text-align:center;font-size:12px;color:var(--muted);font-weight:500;margin-right:40px}
.browser img{width:100%;display:block}
.phone-f{border-radius:46px;padding:10px;background:linear-gradient(160deg,#3A3A42,#0D0D10);box-shadow:0 40px 80px -24px rgba(20,20,40,.5),inset 0 0 0 1.5px rgba(255,255,255,.15)}
.phone-f img{border-radius:37px;width:100%;display:block;aspect-ratio:390/844;object-fit:cover;object-position:top}
.stage .phone-f{position:absolute;width:250px;left:-10px;bottom:-10px}
/* On phones, browser frames become phone frames showing the real mobile screenshot */
@media(max-width:760px){
  .browser.adapt{max-width:270px;margin-left:auto;margin-right:auto;border-radius:40px;padding:8px;background:linear-gradient(160deg,#3A3A42,#0D0D10);box-shadow:0 30px 60px -20px rgba(20,20,40,.45)}
  .browser.adapt .bar{display:none}
  .browser.adapt img{border-radius:32px;aspect-ratio:390/844;object-fit:cover;object-position:top}
  .stage{display:flex;justify-content:center;align-items:flex-end;gap:12px;padding:0 0 10px}
  .stage .browser.adapt,.stage .phone-f{position:static;width:48%;max-width:220px;margin:0}
  .stage .phone-f{transform:translateY(24px)}
}
.stage .chip{position:absolute;right:-8px;top:90px;padding:12px 14px;border-radius:16px;display:flex;gap:10px;align-items:center;font-size:13px;text-align:left;background:rgba(255,255,255,.78);-webkit-backdrop-filter:saturate(180%) blur(18px);backdrop-filter:saturate(180%) blur(18px);border:1px solid rgba(255,255,255,.9);box-shadow:var(--glass-shadow)}
.stage .chip .ic{width:36px;height:36px;border-radius:11px;display:flex;align-items:center;justify-content:center;color:#fff}
@media(max-width:760px){.stage .chip{display:none}}
/* sections */
section.s{padding:96px 0}
.s-head{max-width:720px;margin:0 auto;text-align:center}
.s-head .k{font-size:13px;font-weight:650;letter-spacing:.06em;text-transform:uppercase;color:var(--brand)}
.s-head h2{font-size:clamp(32px,4.6vw,54px);letter-spacing:-.045em;margin-top:12px;line-height:1.02;font-weight:740}
.s-head p{color:var(--muted);font-size:18px;margin-top:16px;line-height:1.55}
.row-feature{display:grid;gap:36px;align-items:center;margin-top:72px}
@media(min-width:900px){.row-feature{grid-template-columns:.85fr 1.15fr;gap:64px}.row-feature.flip{grid-template-columns:1.15fr .85fr}.row-feature.flip .txt{order:2}}
.row-feature .num{width:38px;height:38px;border-radius:12px;background:var(--ink);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700}
.row-feature h3{font-size:clamp(26px,3vw,36px);letter-spacing:-.035em;margin-top:18px;font-weight:720;line-height:1.1}
.row-feature p{color:var(--muted);font-size:17px;margin-top:14px;line-height:1.6}
.row-feature ul{list-style:none;padding:0;margin:20px 0 0}
.row-feature li{display:flex;gap:10px;align-items:flex-start;margin-top:10px;font-size:15.5px}
.row-feature li svg{color:var(--brand);margin-top:2px}
.phones{display:grid;grid-template-columns:repeat(3,1fr);gap:18px;margin-top:56px;align-items:end}
.phones .phone-f{border-radius:40px}
.phones .phone-f:nth-child(2){transform:translateY(-30px)}
.phones figcaption{text-align:center;margin-top:14px;font-size:14px;font-weight:550;color:var(--ink-2)}
@media(max-width:760px){.phones{grid-template-columns:repeat(3,64%);overflow-x:auto;scroll-snap-type:x mandatory;padding:10px 20px 20px;margin:40px -20px 0}.phones figure{scroll-snap-align:center}.phones .phone-f:nth-child(2){transform:none}}
.phones figure{margin:0}
.dark{background:#0B0B0F;color:#fff;border-radius:40px;margin:0 12px;overflow:hidden;position:relative}
.dark::before{content:"";position:absolute;width:700px;height:700px;border-radius:50%;background:radial-gradient(circle,rgba(79,70,229,.45),transparent 65%);top:-200px;right:-150px}
.dark::after{content:"";position:absolute;width:600px;height:600px;border-radius:50%;background:radial-gradient(circle,rgba(240,86,42,.35),transparent 65%);bottom:-250px;left:-150px}
.dark .wrap{position:relative;z-index:1}
.dark .s-head p,.dark .row-feature p{color:#A1A1AA}
.dark .row-feature .num{background:#fff;color:#111}
.games-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:10px;margin-top:22px}
.games-grid div{display:flex;gap:10px;align-items:center;padding:12px;border-radius:16px;background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.1);font-weight:600;font-size:14.5px}
.games-grid .gt{width:44px;height:36px;border-radius:9px;overflow:hidden;flex:none}
.games-grid .gt svg{width:100%;height:100%}
.feat{display:grid;gap:14px;margin-top:56px}
@media(min-width:700px){.feat{grid-template-columns:repeat(2,1fr)}}
@media(min-width:1000px){.feat{grid-template-columns:repeat(3,1fr)}}
.f{padding:26px;border-radius:24px;background:#fff;border:1px solid var(--line-2);box-shadow:var(--shadow-xs);transition:box-shadow .25s,transform .25s}
.f:hover{box-shadow:var(--shadow);transform:translateY(-2px)}
.f .ic{width:44px;height:44px;border-radius:14px;background:var(--brand-soft);color:var(--brand-deep);display:flex;align-items:center;justify-content:center}
.f h3{font-size:17px;margin-top:16px;font-weight:650}
.f p{color:var(--muted);margin-top:6px;font-size:15px;line-height:1.55}
.prices{display:grid;gap:16px;margin-top:56px}
@media(min-width:900px){.prices{grid-template-columns:repeat(3,1fr)}}
.pr{background:#fff;border:1px solid var(--line-2);border-radius:28px;padding:30px;display:flex;flex-direction:column;box-shadow:var(--shadow-xs)}
.pr.pop{background:#0B0B0F;color:#fff;border-color:#0B0B0F;box-shadow:0 30px 60px -20px rgba(0,0,0,.4)}
.pr.pop .muted{color:#A1A1AA}
.pr .amt{font-size:44px;font-weight:760;letter-spacing:-.045em;margin-top:16px}
.pr ul{list-style:none;padding:0;margin:22px 0 28px;flex:1}
.pr li{display:flex;gap:10px;margin-top:11px;font-size:15px}
.pr li svg{color:var(--ok);margin-top:2px}
.pr.pop li svg{color:#4ADE80}
.pr.pop .btn{background:#fff;color:#111;border-color:#fff}
details{background:#fff;border:1px solid var(--line-2);border-radius:18px;padding:20px 24px;margin-top:10px}
summary{font-weight:620;cursor:pointer;font-size:16px;list-style:none;display:flex;justify-content:space-between;align-items:center;gap:12px}
summary::-webkit-details-marker{display:none}
summary svg{transition:transform .25s;color:var(--muted)}
details[open] summary svg{transform:rotate(45deg)}
details p{color:var(--muted);margin-top:12px;line-height:1.6}
.final{text-align:center;padding:88px 24px;border-radius:40px;margin:0 12px;color:#fff;background:radial-gradient(800px 400px at 20% 0%,#FF8A5B,transparent 60%),radial-gradient(800px 500px at 100% 100%,#7C3AED,transparent 60%),#C2410C}
.final h2{font-size:clamp(34px,5vw,58px);letter-spacing:-.045em;font-weight:760;line-height:1.02}
footer{padding:48px 0 40px;color:var(--muted);font-size:14px}
.foot-grid{display:grid;gap:28px}
@media(min-width:800px){.foot-grid{grid-template-columns:1.4fr 1fr 1fr}}
.foot-grid h4{font-size:13px;font-weight:650;color:var(--ink);margin-bottom:10px}
.foot-grid a{display:block;text-decoration:none;padding:4px 0;color:var(--muted)}
.foot-grid a:hover{color:var(--ink)}
.byline{display:flex;justify-content:space-between;flex-wrap:wrap;gap:10px;margin-top:36px;padding-top:22px;border-top:1px solid var(--line-2)}
.byline a{color:var(--ink);font-weight:650;text-decoration:none}
.byline a:hover{color:var(--brand)}
.reveal{opacity:0;transform:translateY(24px);transition:opacity .8s var(--ease),transform .8s var(--ease)}
.reveal.in{opacity:1;transform:none}
@media (prefers-reduced-motion:reduce){.reveal{opacity:1;transform:none}}
</style>
</head>
<body>
<header class="nav">
    <div class="nav-in">
        <x-brand />
        <nav class="links" aria-label="Sections"><a href="#how">How it works</a><a href="#customers">For customers</a><a href="#games">Games</a><a href="#pricing">Pricing</a></nav>
        <span class="grow"></span>
        @auth
            <a href="{{ auth()->user()->is_admin ? route('admin.index') : route('dashboard') }}" class="btn btn-primary btn-sm">Dashboard</a>
        @else
            <a href="{{ route('login') }}" class="login">Log in</a>
            <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Start free</a>
        @endauth
    </div>
</header>

<main>
<section class="hero">
    <div class="wrap">
        <h1>The menu your café deserves, <span class="soft">in every hand.</span></h1>
        <p class="lead">Sign up, add your dishes, pick your colours and print your QR codes. Your digital menu is live in under 30 minutes, and you can change a price from your phone any time.</p>
        <div class="cta">
            <a href="{{ route('register') }}" class="btn btn-brand btn-lg">Create your menu free<x-icon name="arrow-right" class="sm"/></a>
            @if ($demo)<a href="{{ url($demo) }}" target="_blank" rel="noopener" class="btn btn-lg btn-glass"><x-icon name="smartphone" class="sm"/>Open the live demo</a>@endif
        </div>
        <div class="trust">
            <span><x-icon name="check-circle" class="sm"/>{{ $trialDays }}-day free trial</span>
            <span><x-icon name="check-circle" class="sm"/>No app to download</span>
            <span><x-icon name="check-circle" class="sm"/>Telebirr &amp; bank details</span>
        </div>

        <div class="stage reveal">
            <div class="browser adapt">
                <div class="bar"><i></i><i></i><i></i><span>xmenu · Dashboard</span></div>
                <picture><source media="(max-width:760px)" srcset="{{ $s('owner-m-dash.webp') }}"><img src="{{ $s('owner-d-dash.webp') }}" alt="Xmenu owner dashboard with big shortcuts" width="1440" height="900" fetchpriority="high"></picture>
            </div>
            <div class="phone-f"><img src="{{ $s('menu-mobile.png') }}" alt="Customer view of a café menu on a phone" width="390" height="844"></div>
            <div class="chip"><span class="ic" style="background:#16A34A"><x-icon name="zap" class="sm"/></span><div><b>Price changed</b><br><span class="tiny muted">Live on the next scan</span></div></div>
        </div>
    </div>
</section>

<section class="s" id="how">
    <div class="wrap">
        <div class="s-head reveal"><div class="k">How it works</div><h2>From sign-up to every table in three steps.</h2><p>No designer and no back-and-forth with a print shop. You do it yourself, at your own pace.</p></div>

        <div class="row-feature reveal">
            <div class="txt">
                <div class="num">1</div>
                <h3>Build your menu in minutes.</h3>
                <p>Add categories and dishes with photos, prices, sizes and extras. Already have a spreadsheet? Import it in one go.</p>
                <ul>
                    <li><x-icon name="check" class="sm"/>Change prices right on the list. It saves by itself.</li>
                    <li><x-icon name="check" class="sm"/>Mark a dish sold out with one tap.</li>
                    <li><x-icon name="check" class="sm"/>Fasting, vegetarian, spicy and more labels.</li>
                </ul>
            </div>
            <div class="browser adapt"><div class="bar"><i></i><i></i><i></i><span>Menu</span></div><picture><source media="(max-width:760px)" srcset="{{ $s('owner-m-menu.webp') }}"><img src="{{ $s('owner-d-menu.webp') }}" alt="Menu page with prices and Available buttons" loading="lazy" width="1440" height="900"></picture></div>
        </div>

        <div class="row-feature flip reveal">
            <div class="txt">
                <div class="num">2</div>
                <h3>Make it look like your café.</h3>
                <p>Your colour, layout, header and font on our professional design. Every change shows live on a phone preview before you publish.</p>
                <ul>
                    <li><x-icon name="check" class="sm"/>List, grid or card layouts.</li>
                    <li><x-icon name="check" class="sm"/>Turn off what you don’t need: Wi-Fi, search, filters.</li>
                    <li><x-icon name="check" class="sm"/>Text colours adjust to stay readable.</li>
                </ul>
            </div>
            <div class="browser adapt"><div class="bar"><i></i><i></i><i></i><span>Look &amp; colours</span></div><picture><source media="(max-width:760px)" srcset="{{ $s('owner-m-design.webp') }}"><img src="{{ $s('owner-d-design.webp') }}" alt="Look and colours page with a live phone preview" loading="lazy" width="1440" height="900"></picture></div>
        </div>

        <div class="row-feature reveal">
            <div class="txt">
                <div class="num">3</div>
                <h3>Print beautiful QR codes.</h3>
                <p>Five ready-made designs in your colours. A6 table cards, A5 counter stands or A4 posters, with one code per café or one per table.</p>
                <ul>
                    <li><x-icon name="check" class="sm"/>Print-ready PDF, or the QR as an image.</li>
                    <li><x-icon name="check" class="sm"/>Save paper with 4 cards on one A4 sheet.</li>
                    <li><x-icon name="check" class="sm"/>Optional Wi-Fi details on the card.</li>
                </ul>
            </div>
            <div class="browser adapt"><div class="bar"><i></i><i></i><i></i><span>QR codes</span></div><picture><source media="(max-width:760px)" srcset="{{ $s('owner-m-qr.webp') }}"><img src="{{ $s('owner-d-qr.webp') }}" alt="QR code designs ready to print" loading="lazy" width="1440" height="900"></picture></div>
        </div>
    </div>
</section>

<section class="s" id="customers" style="padding-top:20px">
    <div class="wrap">
        <div class="s-head reveal"><div class="k">For your customers</div><h2>Everything they ask your staff, answered on their phone.</h2><p>Opens in a second on any phone. No app, no sign-up. Just scan.</p></div>
        <div class="phones reveal">
            <figure><div class="phone-f"><img src="{{ $s('menu-mobile-item.webp') }}" alt="Dish details" loading="lazy" width="390" height="844"></div><figcaption>Dish details in one tap</figcaption></figure>
            <figure><div class="phone-f"><img src="{{ $s('menu-mobile.png') }}" alt="Café menu with photos and Wi-Fi" loading="lazy" width="390" height="844"></div><figcaption>Your menu, in your colours</figcaption></figure>
            <figure><div class="phone-f"><img src="{{ $s('menu-mobile-pay.webp') }}" alt="Payment accounts with copy buttons" loading="lazy" width="390" height="844"></div><figcaption>Telebirr and bank details in one tap</figcaption></figure>
        </div>
        <div class="browser reveal" style="margin-top:64px">
            <div class="bar"><i></i><i></i><i></i><span>Also beautiful on a laptop</span></div>
            <img src="{{ $s('menu-desktop.webp') }}" alt="The same menu on a desktop browser" loading="lazy" width="1440" height="900">
        </div>
    </div>
</section>

<section class="s dark" id="games">
    <div class="wrap">
        <div class="row-feature flip reveal" style="margin-top:0">
            <div class="txt">
                <div class="k" style="font-size:13px;font-weight:650;letter-spacing:.06em;text-transform:uppercase;color:#FF8A5B">Games while they wait</div>
                <h3 style="font-size:clamp(30px,4vw,48px)">Food takes 15 minutes. Fun starts right away.</h3>
                <p>Built-in games for two players on one phone, or against the computer. No ads, nothing to install, and they keep working even if the Wi-Fi drops.</p>
                <div class="games-grid">
                    @foreach (\App\Models\Game::where('active', true)->orderBy('sort_order')->get() as $g)
                        <div><span class="gt" style="background:{{ $g->color }}">{!! $g->artSvg() !!}</span>{{ tr($g->name, 'en') }}</div>
                    @endforeach
                </div>
                <ul style="color:#D4D4D8">
                    <li><x-icon name="check" class="sm"/>Easy, medium and hard computer opponents</li>
                    <li><x-icon name="check" class="sm"/>Best-of series, scoreboard and player names</li>
                </ul>
            </div>
            <div style="display:flex;gap:18px;justify-content:center">
                <div class="phone-f" style="width:min(46%,260px)"><img src="{{ $s('game-mobile-c4.webp') }}" alt="Connect Four for two players" loading="lazy" width="390" height="844"></div>
                <div class="phone-f" style="width:min(46%,260px);margin-top:50px"><img src="{{ $s('menu-mobile-games.webp') }}" alt="Games tab on the menu" loading="lazy" width="390" height="844"></div>
            </div>
        </div>
    </div>
</section>

<section class="s">
    <div class="wrap">
        <div class="s-head reveal"><div class="k">Features</div><h2>Small details that make a big difference.</h2></div>
        <div class="feat reveal">
            @foreach ([
                ['zap', 'Opens in about a second', 'Built for everyday Android phones on mobile data. Every menu is cached and served instantly.'],
                ['edit', 'Edit from behind the counter', 'Change a price or mark something sold out from your phone. It’s live on the next scan.'],
                ['leaf', 'Fasting & dietary filters', 'Customers filter by fasting, vegetarian, spicy and more with one tap.'],
                ['card', 'Telebirr, CBE Birr & banks', 'Your account numbers with copy buttons. Customers pay and show the receipt.'],
                ['wifi', 'Wi-Fi in one tap', 'Network and password with a copy button. Staff stop being asked.'],
                ['chart', 'See what people look at', 'Scans per day, most-viewed dishes and which tables scan most.'],
                ['eye', 'Show only what you need', 'Switch off the Wi-Fi card, search, filters or games any time.'],
                ['moon', 'Light & dark', 'Bright and clear by default, with a dark option for evening lounges.'],
                ['table', 'A QR for every table', 'Number your tables, print them all at once, and see which ones are busiest.'],
            ] as [$i, $t, $d])
                <div class="f"><div class="ic"><x-icon :name="$i"/></div><h3>{{ $t }}</h3><p>{{ $d }}</p></div>
            @endforeach
        </div>
    </div>
</section>

<section class="s" id="pricing" style="padding-top:20px">
    <div class="wrap">
        <div class="s-head reveal"><div class="k">Pricing</div><h2>Start free. Upgrade when it pays for itself.</h2><p>Every plan starts with a {{ $trialDays }}-day free trial with everything unlocked. Pay monthly by Telebirr or bank transfer.</p></div>
        @if ($plans->isNotEmpty())
        <div class="prices reveal">
            @foreach ($plans as $p)
                <div class="pr {{ $loop->index === 1 ? 'pop' : '' }}">
                    <div class="row between"><b style="font-size:19px">{{ $p->name }}</b>@if ($loop->index === 1)<span class="badge plain" style="background:rgba(255,255,255,.12);color:#fff">Most popular</span>@endif</div>
                    @if ($p->tagline)<span class="small muted mt-1">{{ $p->tagline }}</span>@endif
                    <div class="amt">{{ number_format($p->price) }} <span class="small muted" style="font-weight:500;letter-spacing:0">{{ $p->currency }} / month</span></div>
                    <ul>@foreach ($p->features ?? [] as $f)<li><x-icon name="check" class="sm"/>{{ $f }}</li>@endforeach</ul>
                    <a href="{{ route('register') }}" class="btn btn-block btn-lg {{ $loop->index === 1 ? '' : 'btn-primary' }}">Start free trial</a>
                </div>
            @endforeach
        </div>
        @endif
    </div>
</section>

<section class="s" id="faq" style="padding-top:0">
    <div class="wrap" style="max-width:820px">
        <div class="s-head reveal"><div class="k">Questions</div><h2>Good to know</h2></div>
        <div class="mt-8">
            @foreach ([
                ['Do my customers need to install an app?', 'No. They scan the QR code with their phone camera and the menu opens in the browser.'],
                ['Can I change prices after I print the QR codes?', 'Yes, that’s the point. The QR code always points to your live menu. Change anything any time; you never need to reprint.'],
                ['Can I hide the Wi-Fi password, search or filters?', 'Yes. On the Look & colours page, switch off anything you don’t want customers to see.'],
                ['Can customers order or pay through Xmenu?', 'Not yet. Customers see your menu and your payment accounts, then order with your waiter. Your current way of working stays the same.'],
                ['What if I don’t have photos?', 'Your menu looks clean and professional without photos too. Add them whenever you’re ready.'],
                ['How do I print the QR codes?', 'Download the PDF from the QR codes page and print it at 100% scale at any print shop or on an office printer. A6 cards fit standard table stands.'],
            ] as [$q, $a])
                <details><summary>{{ $q }}<x-icon name="plus" class="sm"/></summary><p>{{ $a }}</p></details>
            @endforeach
        </div>
    </div>
</section>

<section class="final reveal">
    <h2>Your new menu is 30 minutes away.</h2>
    <p style="font-size:18px;opacity:.9;margin-top:14px">Free for {{ $trialDays }} days. No card, no contract.</p>
    <a href="{{ route('register') }}" class="btn btn-lg mt-8" style="background:#fff;color:var(--ink);border-color:#fff">Create your menu free<x-icon name="arrow-right" class="sm"/></a>
</section>
</main>

<footer>
    <div class="wrap">
        <div class="foot-grid">
            <div>
                <x-brand style="font-size:17px" />
                <p style="margin-top:12px;max-width:320px;line-height:1.55">QR menus for cafés and restaurants in Ethiopia.</p>
            </div>
            <div>
                <h4>Product</h4>
                <a href="#how">How it works</a>
                <a href="#pricing">Pricing</a>
                @if ($demo)<a href="{{ url($demo) }}" target="_blank" rel="noopener">Live demo</a>@endif
                <a href="{{ route('register') }}">Create your menu</a>
                <a href="{{ route('login') }}">Log in</a>
            </div>
            <div>
                <h4>Help</h4>
                @if (! empty($support['phone']))<a href="tel:{{ preg_replace('/[^\d+]/', '', $support['phone']) }}">{{ $support['phone'] }}</a>@endif
                @if (! empty($support['telegram']))<a href="https://t.me/{{ ltrim($support['telegram'], '@') }}" target="_blank" rel="noopener">Telegram {{ '@'.ltrim($support['telegram'], '@') }}</a>@endif
                @if (! empty($support['email']))<a href="mailto:{{ $support['email'] }}">{{ $support['email'] }}</a>@endif
                <a href="{{ route('legal', 'privacy') }}">Privacy</a>
                <a href="{{ route('legal', 'terms') }}">Terms</a>
                <a href="{{ route('legal', 'credits') }}">Photo credits</a>
            </div>
        </div>
        <div class="byline">
            <span>© {{ date('Y') }} Xmenu. Made for cafés in Ethiopia.</span>
            <span>Xmenu by <a href="https://xvalue.et" target="_blank" rel="noopener">Xvalue</a></span>
        </div>
    </div>
</footer>
<script>
(function () {
    var els = document.querySelectorAll('.reveal');
    if (!('IntersectionObserver' in window) || navigator.webdriver) { els.forEach(function (e) { e.classList.add('in'); }); return; }
    var io = new IntersectionObserver(function (es) { es.forEach(function (e) { if (e.isIntersecting) { e.target.classList.add('in'); io.unobserve(e.target); } }); }, { rootMargin: '0px 0px -8% 0px' });
    els.forEach(function (e) { io.observe(e); });
})();
</script>
</body>
</html>
