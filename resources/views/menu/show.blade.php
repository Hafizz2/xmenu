@php
    use App\Support\Icons;
    $cur = $cafe->currency;
    $name = $cafe->name;
    $tagline = tr($cafe->tagline);
    $layout = $theme['layout'];
    $tagDefs = config('xmenu.tags');
    $hasGames = $games->isNotEmpty();
    $showWifi = $cafe->wifi_name && $theme['show_wifi'];
    $socials = collect($cafe->socials ?? [])->filter();
    $mapLink = $cafe->map_url ?: ($cafe->address ? 'https://www.google.com/maps/search/?api=1&query='.urlencode($cafe->name.' '.$cafe->address) : null);
    $other = $lang === 'en' ? 'am' : 'en';
    $sizes = ['list' => '104px', 'grid' => '(min-width:900px) 280px, 50vw', 'cards' => '(min-width:900px) 380px, 100vw'][$layout];
    $imgW = ['list' => 320, 'grid' => 320, 'cards' => 640][$layout];
    $sheetData = [];
    $n = 0;
    $payColors = ['bank' => '#1D4ED8', 'telebirr' => '#0E9F6E', 'cbe_birr' => '#7E22CE', 'other' => '#475569'];
    $use = fn ($id, $cls = '') => '<svg class="ic '.$cls.'" aria-hidden="true"><use href="#i-'.$id.'"/></svg>';
    $spriteNames = array_merge(
        ['search', 'x', 'globe', 'phone', 'pin', 'wifi', 'lock', 'copy', 'check', 'info', 'book', 'game', 'card', 'receipt', 'clock', 'external', 'play', 'left', 'users', 'cpu', 'utensils', 'arrow-right', 'link', 'help', 'heart', 'right'],
        array_column($tagDefs, 'icon'),
        $categories->pluck('icon')->filter()->all()
    );
    $gameData = $games->mapWithKeys(fn ($g) => [$g->id => [
        'n' => tr($g->name), 'd' => tr($g->description), 'u' => $g->url(), 'p' => $g->players, 'ai' => $g->has_ai,
        'm' => \App\Models\Game::MODES[$g->mode] ?? '', 'c' => $g->color, 'h' => $g->hosted,
    ]]);
@endphp
<!doctype html>
<html lang="{{ $lang }}" data-mode="{{ $theme['mode'] }}">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title>{{ $name }} · {{ $ui['menu'] }}</title>
<meta name="description" content="{{ $tagline ?: $name.' menu, prices, Wi-Fi and payment info.' }}">
<meta name="theme-color" content="{{ $theme['color'] }}">
<meta property="og:title" content="{{ $name }}">
<meta property="og:description" content="{{ $tagline ?: $ui['menu'] }}">
@if ($cafe->cover)<meta property="og:image" content="{{ $cafe->coverUrl(1024) }}">@elseif ($cafe->logo)<meta property="og:image" content="{{ $cafe->logoUrl(512) }}">@endif
@if ($cafe->logo)<link rel="icon" href="{{ $cafe->logoUrl(128) }}">@endif
@if ($preview)<meta name="robots" content="noindex">@endif
<style>
:root{--brand:{{ $theme['color'] }};--on-brand:{{ $theme['ink'] }};--brand-soft:{{ $theme['soft'] }};--brand-text:{{ $theme['text_light'] }};--bg:{{ $theme['bg'] }};--card:{{ $theme['card'] }};--line:rgba(30,20,10,.08);--text:#1B1A1F;--muted:#6E6A73;--sold:#C42B2B;--r:{{ $theme['radius_px'] }}px;--font:{!! $theme['font_stack'] !!};--shadow:0 1px 2px rgba(60,40,20,.05),0 6px 20px -10px rgba(60,40,20,.16);--glass:rgba(255,255,255,.88);--glass-edge:rgba(255,255,255,.9);--glass-shadow:0 10px 30px -10px rgba(60,40,20,.28),0 1px 3px rgba(0,0,0,.06);--chip:#fff;color-scheme:light}
@php $dark = '--bg:#0C0C0F;--card:#17171B;--line:rgba(255,255,255,.08);--text:#F4F4F6;--muted:#9C9CA6;--brand-soft:'.$theme['soft_dark'].';--brand-text:'.$theme['text_dark'].';--sold:#FF6B6B;--shadow:none;--glass:rgba(30,30,36,.82);--glass-edge:rgba(255,255,255,.1);--glass-shadow:0 16px 40px -12px rgba(0,0,0,.7);--chip:#222228;color-scheme:dark'; @endphp
html[data-mode=dark]{ {!! $dark !!} }
@media (prefers-color-scheme:dark){html[data-mode=auto]{ {!! $dark !!} }}
*{box-sizing:border-box;-webkit-tap-highlight-color:transparent}
[hidden]{display:none!important}
html{-webkit-text-size-adjust:100%}
body{margin:0;font-family:var(--font);font-size:16px;line-height:1.45;color:var(--text);background:var(--bg);-webkit-font-smoothing:antialiased}
h1,h2,h3,h4,p{margin:0}
button{font:inherit;color:inherit}
a{color:inherit}
img{display:block;max-width:100%}
.ic{width:20px;height:20px;stroke:currentColor;fill:none;stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round;flex:none}
.ic.s{width:15px;height:15px}
.app{max-width:1200px;margin:0 auto;min-height:100vh;padding-bottom:calc(112px + env(safe-area-inset-bottom))}
.sr{position:absolute;width:1px;height:1px;overflow:hidden;clip:rect(0 0 0 0)}
.glass{background:var(--glass);-webkit-backdrop-filter:saturate(180%) blur(20px);backdrop-filter:saturate(180%) blur(20px);box-shadow:var(--glass-shadow);border:1px solid var(--glass-edge)}

/* Header: photo banner, then the café's name on a light background */
.hero{position:relative}
.banner{position:relative;height:190px;overflow:hidden;background:linear-gradient(135deg,color-mix(in srgb,var(--brand) 55%,#fff),var(--brand))}
.banner img{width:100%;height:100%;object-fit:cover}
.banner::after{content:"";position:absolute;inset:0;background:linear-gradient(180deg,rgba(0,0,0,.18),rgba(0,0,0,0) 40%)}
.ident{position:relative;text-align:center;padding:0 20px 6px}
.ident .logo,.ident .mono{width:88px;height:88px;border-radius:calc(var(--r) + 10px);margin:-44px auto 12px;object-fit:cover;background:#fff;border:4px solid var(--bg);box-shadow:0 8px 20px -8px rgba(0,0,0,.3);position:relative}
.ident .mono{display:flex;align-items:center;justify-content:center;font-size:38px;font-weight:800;background:var(--brand);color:var(--on-brand)}
.h-minimal .ident{padding-top:64px}
.h-minimal .ident .logo,.h-minimal .ident .mono{margin-top:0}
.ident h1{font-size:28px;font-weight:800;letter-spacing:-.025em;line-height:1.1}
.ident .tag{margin-top:6px;font-size:15px;color:var(--muted)}
.meta{display:flex;justify-content:center;flex-wrap:wrap;gap:8px;margin-top:14px}
.pill{display:inline-flex;align-items:center;gap:7px;height:34px;padding:0 14px;border-radius:99px;font-size:14px;font-weight:650;background:var(--card);box-shadow:inset 0 0 0 1px var(--line);text-decoration:none;color:var(--text);border:0;cursor:pointer}
.pill .ic{width:16px;height:16px;color:var(--muted)}
.status i{width:9px;height:9px;border-radius:50%;background:#9CA3AF}
.status.open{color:#137333;background:#E8F6EC;box-shadow:none}
.status.open i{background:#22A447;box-shadow:0 0 0 3px rgba(34,164,71,.2)}
.status.closed{color:#B3261E;background:#FCECEA;box-shadow:none}
.status.closed i{background:#E5484D}
.lang{position:absolute;top:calc(14px + env(safe-area-inset-top));right:14px;z-index:3;height:40px;padding:0 15px;border-radius:99px;display:flex;align-items:center;gap:7px;font-size:14.5px;font-weight:700;text-decoration:none;color:var(--text)}
.lang .ic{color:var(--brand)}

/* Wi-Fi card */
.wifi{margin:18px 16px 0;display:flex;align-items:center;gap:13px;padding:13px 13px 13px 15px;background:var(--card);border-radius:calc(var(--r) + 4px);box-shadow:inset 0 0 0 1px var(--line),var(--shadow)}
.wifi .wi{width:44px;height:44px;border-radius:14px;background:var(--brand-soft);color:var(--brand-text);display:flex;align-items:center;justify-content:center;flex:none}
.wifi .t{flex:1;min-width:0;line-height:1.3}
.wifi small{display:block;font-size:12.5px;color:var(--muted);font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.wifi b{display:block;font-size:15.5px;font-weight:700;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.wifi b .mono{color:var(--brand-text)}

/* Search & categories */
.tools{padding:16px 16px 0}
.search{position:relative;display:block}
.search>.ic{position:absolute;left:16px;top:50%;transform:translateY(-50%);color:var(--muted)}
.search input{width:100%;height:52px;border-radius:99px;border:0;background:var(--card);box-shadow:inset 0 0 0 1px var(--line),var(--shadow);color:var(--text);font:16.5px var(--font);padding:0 48px;-webkit-appearance:none;appearance:none}
.search input::placeholder{color:var(--muted)}
.search input:focus{outline:none;box-shadow:inset 0 0 0 2px var(--brand)}
.search .x{position:absolute;right:7px;top:50%;transform:translateY(-50%);width:38px;height:38px;border:0;background:var(--brand-soft);color:var(--brand-text);border-radius:50%;display:none;align-items:center;justify-content:center;cursor:pointer}
.search.has .x{display:flex}
.bar{position:sticky;top:0;z-index:20;margin-top:12px;transition:background .2s,box-shadow .2s}
.bar.stuck{background:var(--glass);-webkit-backdrop-filter:saturate(180%) blur(20px);backdrop-filter:saturate(180%) blur(20px);box-shadow:0 6px 20px -14px rgba(0,0,0,.35)}
.chips{display:flex;gap:8px;overflow-x:auto;padding:10px 16px;scrollbar-width:none;scroll-behavior:smooth}
.chips::-webkit-scrollbar{display:none}
.chip{flex:none;height:44px;padding:0 17px 0 14px;border-radius:99px;border:0;background:var(--chip);box-shadow:inset 0 0 0 1px var(--line);font-size:15px;font-weight:650;white-space:nowrap;cursor:pointer;display:inline-flex;align-items:center;gap:8px;text-decoration:none;color:var(--text);transition:background .2s,color .2s,box-shadow .2s}
.chip .ic{width:19px;height:19px;color:var(--brand)}
.chip.on{background:var(--brand);color:var(--on-brand);box-shadow:0 6px 16px -8px var(--brand)}
.chip.on .ic{color:currentColor}
.filters{display:flex;gap:7px;overflow-x:auto;padding:2px 16px 4px;scrollbar-width:none}
.filters::-webkit-scrollbar{display:none}
.filters .chip{height:34px;font-size:13.5px;padding:0 12px 0 10px;font-weight:600;color:var(--muted)}
.filters .chip .ic{width:15px;height:15px}
.filters .chip.on{background:var(--brand-soft);color:var(--brand-text);box-shadow:inset 0 0 0 1.5px var(--brand)}

/* Most loved: big swipeable photo cards */
.pop{padding:22px 0 2px}
.sec-h{display:flex;align-items:baseline;justify-content:space-between;gap:10px;padding:0 16px;margin-bottom:12px}
.sec-h h2{font-size:21px;font-weight:800;letter-spacing:-.02em;display:flex;align-items:center;gap:8px}
.sec-h h2 .ic{color:var(--brand)}
.sec-h small{color:var(--muted);font-size:13px}
.pop-row{display:flex;gap:12px;overflow-x:auto;padding:2px 16px 14px;scroll-padding-inline:16px;scroll-snap-type:x mandatory;scrollbar-width:none}
.pop-row::-webkit-scrollbar{display:none}
.pc{flex:none;width:68%;max-width:270px;scroll-snap-align:start;padding:0;border:0;background:var(--card);border-radius:calc(var(--r) + 4px);overflow:hidden;box-shadow:inset 0 0 0 1px var(--line),var(--shadow);text-align:left;cursor:pointer;color:var(--text);transition:transform .15s}
.pc:active{transform:scale(.97)}
.pc img{width:100%;aspect-ratio:4/3;object-fit:cover;background:var(--line)}
.pc div{padding:11px 14px 13px;display:flex;justify-content:space-between;align-items:baseline;gap:8px}
.pc b{font-size:16px;font-weight:700;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}

/* Sections & items */
.cat{padding:22px 16px 4px;scroll-margin-top:70px}
.cat>h2{font-size:22px;font-weight:800;letter-spacing:-.02em;margin-bottom:14px;display:flex;align-items:center;gap:10px}
.cat>h2 .badge-ic{width:38px;height:38px;border-radius:12px;background:var(--brand-soft);color:var(--brand-text);display:inline-flex;align-items:center;justify-content:center}
.cat>h2 .cnt{margin-left:auto;font-size:13px;font-weight:600;color:var(--muted);letter-spacing:0}
.item{position:relative;cursor:pointer;background:var(--card);border-radius:var(--r);box-shadow:inset 0 0 0 1px var(--line),var(--shadow);overflow:hidden;transition:transform .15s,box-shadow .2s;text-align:left;width:100%;padding:0;display:flex;flex-direction:column;border:0;color:var(--text)}
.item:active{transform:scale(.98)}
@media(hover:hover){.item:hover,.pc:hover{box-shadow:inset 0 0 0 1px var(--line),0 14px 34px -16px rgba(60,40,20,.35)}}
.item h3{font-size:16.5px;font-weight:700;line-height:1.25;letter-spacing:-.01em}
.desc{color:var(--muted);font-size:14px;margin-top:4px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;line-height:1.4}
.price{font-weight:800;font-size:17px;white-space:nowrap;color:var(--brand-text);font-variant-numeric:tabular-nums;letter-spacing:-.01em}
.price small{font-size:12px;font-weight:650;color:var(--muted);margin-left:3px}
.price .from{font-size:12px;font-weight:600;color:var(--muted);margin-right:4px}
.tags{display:flex;gap:5px;margin-top:8px;flex-wrap:wrap}
.tags span{display:inline-flex;align-items:center;gap:4px;font-size:12px;line-height:1;padding:5px 8px 5px 6px;border-radius:99px;background:var(--brand-soft);color:var(--brand-text);font-weight:650}
.tags .ic{width:13px;height:13px;stroke-width:2}
.sold .item-media,.sold h3,.sold .desc,.sold .price{opacity:.45}
.sold .item-media{filter:grayscale(1)}
.sold-badge{display:inline-flex;font-size:11px;font-weight:800;letter-spacing:.06em;text-transform:uppercase;color:#fff;background:var(--sold);border-radius:6px;padding:4px 7px}
.media-wrap{position:relative}
.media-wrap .sold-badge{position:absolute;left:10px;top:10px}
.ph{display:flex;align-items:center;justify-content:center;background:linear-gradient(150deg,var(--brand-soft),color-mix(in srgb,var(--brand-soft) 70%,var(--brand)));color:var(--brand-text);font-weight:800;font-size:34px}
.item-media{object-fit:cover;background:var(--line);width:100%}
.body{flex:1;display:flex;flex-direction:column;min-width:0}
.foot-row{display:flex;align-items:center;justify-content:space-between;gap:8px;margin-top:auto;padding-top:10px}
.more{width:32px;height:32px;border-radius:50%;background:var(--brand-soft);color:var(--brand-text);display:inline-flex;align-items:center;justify-content:center;flex:none}
.more .ic{width:16px;height:16px;stroke-width:2.2}
/* list: photo on the left */
.l-list .items{display:grid;gap:10px}
.l-list .item{flex-direction:row;gap:14px;padding:12px;align-items:stretch}
.l-list .media-wrap{flex:none;width:104px}
.l-list .item-media,.l-list .ph{width:104px;height:104px;border-radius:calc(var(--r) - 2px)}
.l-list .media-wrap .sold-badge{left:6px;top:6px;font-size:10px}
.l-list .body{padding:2px 2px 0 0}
/* grid: two photo tiles per row, equal heights */
.l-grid .items{display:grid;grid-template-columns:1fr 1fr;gap:12px}
.l-grid .item-media,.l-grid .ph{aspect-ratio:1/1;height:auto}
.l-grid .body{padding:11px 12px 12px}
.l-grid h3{font-size:15.5px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
.l-grid .desc{font-size:13px}
.l-grid .tags span:nth-child(n+3){display:none}
.l-grid .foot-row{padding-top:8px}
/* cards: big photo */
.l-cards .items{display:grid;gap:16px}
.l-cards .item-media,.l-cards .ph{aspect-ratio:16/10;height:auto}
.l-cards .body{padding:15px 17px 17px}
.l-cards h3{font-size:19px}
.l-cards .desc{-webkit-line-clamp:3}
.hidden{display:none!important}
.none{text-align:center;padding:56px 24px;color:var(--muted)}
.none .ic{width:34px;height:34px;margin:0 auto 10px;display:block;opacity:.6}
.foot{padding:30px 20px 10px;text-align:center;color:var(--muted);font-size:13.5px}
.foot p+p{margin-top:4px}
.foot .order{display:inline-flex;align-items:center;gap:8px;background:var(--brand-soft);color:var(--brand-text);padding:11px 17px;border-radius:99px;font-weight:700;font-size:15px;margin-bottom:12px}
.powered{display:inline-flex;align-items:center;gap:5px;margin-top:16px;font-size:12px;opacity:.65;text-decoration:none}

/* Tabs: floating dock with clear labels */
.tab{display:none}
.tab.on{display:block;animation:fade .25s ease}
.tab:not(#tab-menu){min-height:100vh}
@keyframes fade{from{opacity:.4;transform:translateY(6px)}to{opacity:1;transform:none}}
.tabbar{position:fixed;left:50%;bottom:calc(10px + env(safe-area-inset-bottom));transform:translateX(-50%);width:calc(100% - 20px);max-width:460px;z-index:30;display:flex;gap:4px;padding:6px;border-radius:26px}
.tabbar button{flex:1;height:58px;border:0;background:none;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:4px;font-size:12.5px;font-weight:700;color:var(--muted);cursor:pointer;border-radius:20px;transition:background .25s,color .25s}
.tabbar button .ic{width:22px;height:22px}
.tabbar button.on{color:var(--on-brand);background:var(--brand);box-shadow:0 6px 14px -8px var(--brand)}
.pane{padding:24px 16px}
.pane-head h2{font-size:26px;font-weight:800;letter-spacing:-.025em}
.pane-head p{color:var(--muted);margin-top:6px}
.boxes{display:grid;gap:14px;margin-top:18px}
.box{background:var(--card);border-radius:calc(var(--r) + 4px);box-shadow:inset 0 0 0 1px var(--line),var(--shadow);overflow:hidden}
.box-h{font-size:12.5px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);padding:16px 18px 2px;display:flex;align-items:center;gap:8px}
.box-h .ic{width:15px;height:15px}
.line{display:flex;align-items:center;gap:13px;padding:14px 18px;min-height:66px}
.line+.line{border-top:1px solid var(--line)}
.line .icb{width:44px;height:44px;border-radius:14px;background:var(--brand-soft);color:var(--brand-text);display:flex;align-items:center;justify-content:center;flex:none}
.line .t{flex:1;min-width:0}
.line .t small{display:block;color:var(--muted);font-size:13px}
.line .t b{display:block;word-break:break-word;font-weight:700;font-size:16px}
.mono{font-family:ui-monospace,"SF Mono",Menlo,Consolas,monospace;letter-spacing:.02em}
.btn{height:44px;padding:0 17px;border-radius:99px;border:0;background:var(--brand);color:var(--on-brand);font-weight:700;font-size:15px;display:inline-flex;align-items:center;gap:7px;cursor:pointer;text-decoration:none;flex:none;white-space:nowrap;box-shadow:0 6px 14px -8px var(--brand);transition:transform .1s}
.btn:active{transform:scale(.96)}
.btn .ic{width:17px;height:17px}
.btn.soft{background:var(--brand-soft);color:var(--brand-text);box-shadow:none}
.btn.done{background:#22A447;color:#fff}
.btn.big{height:54px;padding:0 26px;font-size:16.5px;width:100%;justify-content:center}
.hours{width:100%;border-collapse:collapse;font-size:15.5px;margin-top:6px}
.hours td{padding:10px 18px}
.hours td:last-child{text-align:right;color:var(--muted);font-variant-numeric:tabular-nums}
.hours tr.today td{font-weight:700;color:var(--text);background:var(--brand-soft)}
.socials{display:flex;flex-wrap:wrap;gap:8px;padding:14px 18px 18px}
.pb{width:44px;height:44px;border-radius:14px;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:800;font-size:12px;flex:none;letter-spacing:-.02em;box-shadow:inset 0 1px 0 rgba(255,255,255,.25)}
.note{display:flex;gap:11px;align-items:flex-start;padding:15px 18px;border-radius:calc(var(--r) + 4px);background:var(--brand-soft);color:var(--brand-text);font-size:14.5px;font-weight:600}
.games{display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-top:18px}
.game{padding:0;border-radius:calc(var(--r) + 4px);overflow:hidden;background:var(--card);box-shadow:inset 0 0 0 1px var(--line),var(--shadow);text-align:left;cursor:pointer;border:0;color:var(--text);transition:transform .15s}
.game:active{transform:scale(.97)}
.game .art{aspect-ratio:1.2;display:flex;align-items:center;justify-content:center;position:relative}
.game .art svg{width:82%;height:82%}
.game .art .ai{position:absolute;top:10px;left:10px;font-size:10.5px;font-weight:750;letter-spacing:.04em;text-transform:uppercase;padding:4px 8px;border-radius:99px;background:rgba(255,255,255,.22);color:#fff;-webkit-backdrop-filter:blur(8px);backdrop-filter:blur(8px)}
.game .gb{padding:11px 13px 13px}
.game b{display:block;font-size:15.5px;font-weight:700}
.game small{color:var(--muted);font-size:13px;display:flex;align-items:center;gap:5px;margin-top:2px}
.game small .ic{width:13px;height:13px}

/* Bottom sheet / desktop dialog */
.scrim{position:fixed;inset:0;z-index:40;background:rgba(20,15,10,.45);-webkit-backdrop-filter:blur(3px);backdrop-filter:blur(3px);opacity:0;pointer-events:none;transition:opacity .3s}
.scrim.on{opacity:1;pointer-events:auto}
.sheet{position:fixed;left:50%;bottom:0;z-index:41;width:100%;max-width:560px;max-height:92vh;overflow-y:auto;background:var(--card);border-radius:28px 28px 0 0;transform:translate(-50%,105%);transition:transform .38s cubic-bezier(.2,.85,.25,1);padding-bottom:calc(18px + env(safe-area-inset-bottom));overscroll-behavior:contain;box-shadow:0 -10px 40px rgba(0,0,0,.2)}
.sheet{visibility:hidden}
.sheet.on{transform:translate(-50%,0);visibility:visible}
.sheet .grab{position:sticky;top:0;height:0;z-index:2}
.sheet .grab i{position:absolute;left:50%;top:8px;transform:translateX(-50%);width:40px;height:5px;border-radius:5px;background:rgba(255,255,255,.9);box-shadow:0 0 0 1px rgba(0,0,0,.08)}
.sheet .close{position:absolute;top:14px;right:14px;z-index:3;width:44px;height:44px;border-radius:50%;border:0;display:flex;align-items:center;justify-content:center;cursor:pointer;color:var(--text)}
.sheet .close .ic{width:20px;height:20px;stroke-width:2.2}
.sheet .close:focus{outline:none}
.sheet .close:focus-visible{box-shadow:0 0 0 3px var(--brand)}
.sheet .simg{width:100%;aspect-ratio:4/3;object-fit:cover;background:var(--line)}
.sheet .sart{width:100%;aspect-ratio:16/10;display:flex;align-items:center;justify-content:center}
.sheet .sart svg{width:60%;height:80%}
.sheet .sbody{padding:22px}
.sheet .sbody.pad-top{padding-top:64px}
.sheet h3{font-size:25px;font-weight:800;letter-spacing:-.02em;padding-right:40px}
.sheet .sbody>.price{display:block;font-size:24px;margin-top:6px}
.sheet .sdesc{color:var(--muted);margin-top:12px;font-size:16px;line-height:1.6;white-space:pre-line}
.opts{margin-top:20px;background:var(--bg);border-radius:var(--r);padding:4px 16px}
.opts h4{margin:10px 0 4px;font-size:12.5px;text-transform:uppercase;letter-spacing:.07em;color:var(--muted)}
.opt{display:flex;justify-content:space-between;padding:13px 0;border-top:1px solid var(--line);font-size:16px}
.opt b{font-weight:700;font-variant-numeric:tabular-nums}
.gmeta{display:flex;gap:6px;flex-wrap:wrap;margin-top:12px}
.gmeta span{display:inline-flex;align-items:center;gap:5px;font-size:13px;font-weight:650;padding:6px 10px;border-radius:99px;background:var(--brand-soft);color:var(--brand-text)}
.gmeta .ic{width:14px;height:14px}
.sheet .back{margin-top:22px}

/* Game player */
.player{position:fixed;inset:0;z-index:60;background:#0B0B0F;display:none}
.player.on{display:block}
.player iframe{width:100%;height:100%;border:0;display:block}
.player .pclose{position:absolute;top:calc(12px + env(safe-area-inset-top));right:12px;z-index:2;height:40px;padding:0 14px;border-radius:99px;border:0;color:#fff;font-weight:700;display:none;align-items:center;gap:6px;cursor:pointer;background:rgba(40,40,46,.6);-webkit-backdrop-filter:blur(16px);backdrop-filter:blur(16px)}
.player.ext .pclose{display:flex}
.player .load{position:absolute;left:0;top:0;height:3px;background:var(--brand);width:0;transition:width 2s ease-out;z-index:3}

/* ---------- Desktop ---------- */
@media(min-width:900px){
  .app{padding:88px 28px 60px}
  .tabbar{top:16px;bottom:auto;width:auto;max-width:none;padding:5px;border-radius:99px}
  .tabbar button{flex-direction:row;height:44px;padding:0 20px;font-size:14.5px;gap:8px;border-radius:99px}
  .tabbar button .ic{width:19px;height:19px}
  .banner{height:300px;border-radius:28px}
  .ident{text-align:left;display:grid;grid-template-columns:auto 1fr;column-gap:22px;align-items:end;padding:0 32px}
  .ident .logo,.ident .mono{grid-row:span 3;margin:-52px 0 0;width:116px;height:116px}
  .h-minimal .ident{padding-top:20px}
  .ident h1{font-size:40px}
  .ident .tag{font-size:17px}
  .meta{justify-content:flex-start;grid-column:2}
  .lang{top:16px;right:16px}
  .wifi{margin:24px 0 0;max-width:520px}
  .tools{display:flex;gap:14px;align-items:center;padding:22px 0 0}
  .search{flex:0 0 380px}
  .filters{margin:0;padding:0;flex-wrap:wrap}
  .bar{top:76px;border-radius:99px;margin:18px 0 0 -6px;max-width:max-content}
  .bar.stuck{box-shadow:var(--glass-shadow);border:1px solid var(--glass-edge)}
  .chips{flex-wrap:wrap;padding:6px}
  .filters{margin-top:8px}
  .pop{padding-top:30px}
  .sec-h,.pop-row{padding-left:0;padding-right:0}
  .pc{width:260px}
  .cat{padding:30px 0 4px;scroll-margin-top:150px}
  .cat>h2{font-size:26px}
  .l-list .items{grid-template-columns:repeat(2,1fr);gap:14px}
  .l-grid .items{grid-template-columns:repeat(3,1fr);gap:16px}
  .l-grid .item-media,.l-grid .ph{aspect-ratio:4/3}
  .l-cards .items{grid-template-columns:repeat(2,1fr);gap:20px}
  .pane{padding:36px 0;max-width:980px;margin:0 auto}
  .pane-head h2{font-size:34px}
  .boxes{grid-template-columns:1fr 1fr;align-items:start}
  .boxes .wide{grid-column:1/-1}
  .games{grid-template-columns:repeat(4,1fr);gap:16px}
  .sheet{top:50%;bottom:auto;border-radius:28px;max-height:88vh;transform:translate(-50%,-46%) scale(.96);opacity:0;pointer-events:none;transition:transform .3s cubic-bezier(.2,.85,.25,1),opacity .2s}
  .sheet.on{transform:translate(-50%,-50%);opacity:1;pointer-events:auto}
  .sheet .grab{display:none}
}
@media(min-width:1180px){.l-grid .items{grid-template-columns:repeat(4,1fr)}.l-cards .items{grid-template-columns:repeat(3,1fr)}}
@media (prefers-reduced-motion:reduce){*{animation:none!important;transition:none!important}}
</style>
</head>
<body>
{!! Icons::sprite($spriteNames) !!}
<div class="app l-{{ $layout }}">
    @php $hc = $theme['header']; $cover = $hc === 'cover' && $cafe->cover; @endphp
    <header class="hero h-{{ $hc }}" id="hero">
        @if ($hc !== 'minimal')
            <div class="banner">
                @if ($cover)
                    <img src="{{ $cafe->coverUrl(640) }}" srcset="{{ $cafe->coverUrl(640) }} 640w, {{ $cafe->coverUrl(1024) }} 1024w" sizes="(min-width:900px) 1150px, 100vw" alt="" fetchpriority="high">
                @endif
            </div>
        @endif
        @if (multilang())<a class="lang glass" href="?lang={{ $other }}" data-lang="{{ $other }}" aria-label="Change language">{!! $use('globe', 's') !!}{{ $other === 'am' ? 'አማርኛ' : 'English' }}</a>@endif
        <div class="ident">
            @if ($cafe->logo)
                <img class="logo" src="{{ $cafe->logoUrl(256) }}" srcset="{{ $cafe->logoUrl(128) }} 1x, {{ $cafe->logoUrl(256) }} 2x" width="88" height="88" alt="{{ $name }}">
            @else
                <div class="mono" aria-hidden="true">{{ mb_substr($name, 0, 1) }}</div>
            @endif
            <h1>{{ $name }}</h1>
            @if ($tagline)<p class="tag">{{ $tagline }}</p>@endif
            <div class="meta">
                <span class="pill status" id="status" hidden><i></i><span></span></span>
                @if ($cafe->phone)<a class="pill" href="tel:{{ preg_replace('/[^\d+]/', '', $cafe->phone) }}" data-ev="call_tap">{!! $use('phone') !!}{{ $ui['call'] }}</a>@endif
                @if ($mapLink)<a class="pill" href="{{ $mapLink }}" target="_blank" rel="noopener" data-ev="map_tap">{!! $use('pin') !!}{{ $ui['directions'] }}</a>@endif
            </div>
        </div>
    </header>

    <main>
        {{-- MENU --}}
        <section class="tab on" id="tab-menu" data-tab="menu">
            @if ($categories->isEmpty())
                <div class="none">{!! $use('utensils') !!}{{ $ui['empty_menu'] }}</div>
            @else
                @if ($showWifi)
                    <div class="wifi">
                        <span class="wi">{!! $use('wifi') !!}</span>
                        <div class="t">
                            <small>{{ $ui['free_wifi'] }} · {{ $cafe->wifi_name }}</small>
                            <b>@if ($cafe->wifi_password){{ $ui['password'] }}: <span class="mono">{{ $cafe->wifi_password }}</span>@else{{ $cafe->wifi_name }}@endif</b>
                        </div>
                        @if ($cafe->wifi_password)
                            <button class="btn" data-copy="{{ $cafe->wifi_password }}" data-ev="wifi_copy">{!! $use('copy') !!}<span>{{ $ui['copy'] }}</span></button>
                        @endif
                    </div>
                @endif

                @if ($theme['show_search'])
                <div class="tools">
                    <label class="search" id="search">
                        <span class="sr">{{ $ui['search'] }}</span>
                        {!! $use('search') !!}
                        <input type="search" id="q" placeholder="{{ $ui['search'] }}" autocomplete="off" enterkeyhint="search">
                        <button type="button" class="x" id="qx" aria-label="{{ $ui['clear'] }}">{!! $use('x', 's') !!}</button>
                    </label>
                </div>
                @endif

                <div class="bar" id="bar">
                    <nav class="chips" id="chips" aria-label="{{ $ui['menu'] }}">
                        @foreach ($categories as $cat)
                            <a class="chip {{ $loop->first ? 'on' : '' }}" href="#c{{ $cat->id }}" data-cat="c{{ $cat->id }}">{!! $use($cat->icon ?: 'utensils') !!}{{ tr($cat->name) }}</a>
                        @endforeach
                    </nav>
                </div>
                @if ($tags->isNotEmpty() && $theme['show_filters'])
                    <div class="filters" id="filters">
                        @foreach ($tags as $key => $t)
                            <button type="button" class="chip" data-tag="{{ $key }}" aria-pressed="false">{!! $use($t['icon'], 's') !!}{{ $t[$lang] ?? $t['en'] }}</button>
                        @endforeach
                    </div>
                @endif

                @php
                    $loved = $categories->flatMap->items->filter(fn ($i) => $i->image && ! $i->sold_out && in_array('popular', $i->tags ?? []))->take(8);
                @endphp
                @if ($theme['show_popular'] && $loved->count() >= 2)
                    <section class="pop" id="pop">
                        <div class="sec-h"><h2>{!! $use('heart') !!}{{ $ui['popular'] }}</h2><small>{{ $ui['tap_hint'] }}</small></div>
                        <div class="pop-row">
                            @foreach ($loved as $li)
                                <button type="button" class="pc" data-open="{{ $li->id }}">
                                    <img src="{{ img_url($li->image, 640) }}" width="320" height="240" alt="{{ tr($li->name) }}" {!! $loop->index > 1 ? 'loading="lazy" decoding="async"' : '' !!}>
                                    <div><b>{{ tr($li->name) }}</b><span class="price">{{ $li->hasVariants() ? ($lang === 'am' ? 'ከ ' : 'from ') : '' }}{{ money($li->fromPrice()) }}<small>{{ $cur }}</small></span></div>
                                </button>
                            @endforeach
                        </div>
                    </section>
                @endif

                @foreach ($categories as $cat)
                    <section class="cat" id="c{{ $cat->id }}">
                        <h2><span class="badge-ic">{!! $use($cat->icon ?: 'utensils') !!}</span>{{ tr($cat->name) }}<span class="cnt">{{ $cat->items->count() }}</span></h2>
                        <div class="items">
                            @foreach ($cat->items as $item)
                                @php
                                    $n++;
                                    $iname = tr($item->name);
                                    $idesc = tr($item->description);
                                    $from = $item->hasVariants();
                                    $p = money($item->fromPrice());
                                    $search = mb_strtolower(implode(' ', array_filter([$item->name['en'] ?? '', $item->name['am'] ?? '', $item->description['en'] ?? ''])));
                                    $tagList = collect($item->tags ?? [])->filter(fn ($t) => isset($tagDefs[$t]));
                                    $sheetData[$item->id] = [
                                        'n' => $iname, 'd' => $idesc, 'p' => money($item->price), 'f' => $from,
                                        'i' => $item->image ? img_url($item->image, 1024) : null,
                                        'i2' => $item->image ? img_url($item->image, 640) : null,
                                        'v' => collect($item->variants ?? [])->map(fn ($v) => [tr($v['name']), money($v['price'])])->all(),
                                        'a' => collect($item->addons ?? [])->map(fn ($v) => [tr($v['name']), money($v['price'])])->all(),
                                        't' => $tagList->map(fn ($t) => [$tagDefs[$t]['icon'], $tagDefs[$t][$lang] ?? $tagDefs[$t]['en']])->values()->all(),
                                        's' => $item->sold_out,
                                    ];
                                    $priceHtml = '<span class="price">'.($from ? '<span class="from">'.($lang === 'am' ? 'ከ' : 'from').'</span>' : '').e($p).'<small>'.e($cur).'</small></span>';
                                    $lazy = $n > 4 ? 'loading="lazy" decoding="async"' : 'fetchpriority="'.($n === 1 ? 'high' : 'auto').'"';
                                    $soldHtml = $item->sold_out ? '<span class="sold-badge">'.e($ui['sold_out']).'</span>' : '';
                                    $media = $item->image
                                        ? '<img class="item-media" src="'.img_url($item->image, $imgW).'" srcset="'.img_url($item->image, 320).' 320w, '.img_url($item->image, 640).' 640w" sizes="'.$sizes.'" width="320" height="240" alt="'.e($iname).'" '.$lazy.'>'
                                        : '<div class="item-media ph" aria-hidden="true">'.e(mb_substr($iname, 0, 1)).'</div>';
                                    // In the list layout, items without a photo simply skip the thumbnail.
                                    $mediaHtml = ($item->image || $layout !== 'list') ? '<div class="media-wrap">'.$media.$soldHtml.'</div>' : '';
                                    $tagHtml = $tagList->isNotEmpty() ? '<div class="tags">'.$tagList->map(fn ($t) => '<span>'.$use($tagDefs[$t]['icon']).e($tagDefs[$t][$lang] ?? $tagDefs[$t]['en']).'</span>')->implode('').'</div>' : '';
                                @endphp
                                <button type="button" class="item {{ $item->sold_out ? 'sold' : '' }}" data-id="{{ $item->id }}" data-tags="{{ implode(' ', $item->tags ?? []) }}" data-s="{{ $search }}">
                                    {!! $mediaHtml !!}
                                    <div class="body">
                                        <h3>{{ $iname }}</h3>
                                        @if ($idesc)<p class="desc">{{ $idesc }}</p>@endif
                                        {!! $tagHtml !!}
                                        @if (! $mediaHtml && $item->sold_out)<div style="margin-top:8px">{!! $soldHtml !!}</div>@endif
                                        <div class="foot-row">{!! $priceHtml !!}<span class="more" aria-hidden="true">{!! $use('right') !!}</span></div>
                                    </div>
                                </button>
                            @endforeach
                        </div>
                    </section>
                @endforeach
                <div class="none hidden" id="none">{!! $use('search') !!}{{ $ui['no_results'] }}</div>
            @endif

            <footer class="foot">
                <div class="order">{!! $use('info', 's') !!}{{ $ui['order_note'] }}</div>
                <p>{{ $cafe->vat_included ? $ui['vat_incl'] : $ui['vat_excl'] }}
                    @if ($cafe->service_charge_pct > 0) {{ str_replace(':pct', rtrim(rtrim(number_format($cafe->service_charge_pct, 2), '0'), '.'), $ui['service']) }}@endif</p>
                <p>{{ $lang === 'am' ? 'ዋጋዎች በ' : 'Prices in ' }}{{ $cur }}</p>
                <a class="powered" href="{{ url('/') }}">Menu by <b>Xmenu</b></a>
            </footer>
        </section>

        {{-- GAMES --}}
        @if ($hasGames)
            <section class="tab" id="tab-games" data-tab="games">
                <div class="pane">
                    <div class="pane-head"><h2>{{ $ui['games_title'] }}</h2><p>{{ $ui['games_sub'] }}</p></div>
                    <div class="games">
                        @foreach ($games as $g)
                            <button type="button" class="game" data-game="{{ $g->id }}">
                                <div class="art" style="background:{{ $g->color ?? '#6D28D9' }}">
                                    @if ($g->has_ai)<span class="ai">{{ $lang === 'am' ? 'ከኮምፒውተር ጋር' : 'vs computer' }}</span>@endif
                                    {!! $g->artSvg() !!}
                                </div>
                                <div class="gb"><b>{{ tr($g->name) }}</b><small>{!! $use('users') !!}{{ $g->players == 2 ? $ui['players_2'] : $ui['players_1'] }}</small></div>
                            </button>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        {{-- INFO --}}
        <section class="tab" id="tab-info" data-tab="info">
            <div class="pane">
                <div class="pane-head"><h2>{{ $name }}</h2>@if ($tagline)<p>{{ $tagline }}</p>@endif</div>
                <div class="boxes">
                    @if ($showWifi)
                        <div class="box">
                            <div class="box-h">{!! $use('wifi') !!}{{ $ui['wifi'] }}</div>
                            <div class="line">
                                <span class="icb">{!! $use('wifi') !!}</span>
                                <div class="t"><small>{{ $ui['network'] }}</small><b>{{ $cafe->wifi_name }}</b></div>
                            </div>
                            @if ($cafe->wifi_password)
                                <div class="line">
                                    <span class="icb">{!! $use('lock') !!}</span>
                                    <div class="t"><small>{{ $ui['password'] }}</small><b class="mono">{{ $cafe->wifi_password }}</b></div>
                                    <button class="btn" data-copy="{{ $cafe->wifi_password }}" data-ev="wifi_copy">{!! $use('copy') !!}<span>{{ $ui['copy'] }}</span></button>
                                </div>
                            @endif
                        </div>
                    @endif

                    @if ($cafe->phone || $cafe->address)
                        <div class="box">
                            <div class="box-h">{!! $use('phone') !!}{{ $ui['contact'] }}</div>
                            @if ($cafe->phone)
                                <div class="line">
                                    <span class="icb">{!! $use('phone') !!}</span>
                                    <div class="t"><b>{{ $cafe->phone }}</b></div>
                                    <a class="btn" href="tel:{{ preg_replace('/[^\d+]/', '', $cafe->phone) }}" data-ev="call_tap">{{ $ui['call'] }}</a>
                                </div>
                            @endif
                            @if ($cafe->address || $mapLink)
                                <div class="line">
                                    <span class="icb">{!! $use('pin') !!}</span>
                                    <div class="t"><b>{{ $cafe->address }}</b></div>
                                    @if ($mapLink)<a class="btn soft" href="{{ $mapLink }}" target="_blank" rel="noopener" data-ev="map_tap">{{ $ui['directions'] }}</a>@endif
                                </div>
                            @endif
                        </div>
                    @endif

                    @if ($cafe->openingHours->isNotEmpty())
                        <div class="box">
                            <div class="box-h">{!! $use('clock') !!}{{ $ui['hours'] }}</div>
                            <table class="hours" id="hours">
                                @foreach ([1, 2, 3, 4, 5, 6, 0] as $d)
                                    @php $h = $hours[$d] ?? null; @endphp
                                    <tr data-day="{{ $d }}"><td>{{ $ui['days'][$d] }}</td><td>{{ ! $h || $h['x'] ? $ui['closed'] : $h['o'].' – '.$h['c'] }}</td></tr>
                                @endforeach
                            </table>
                        </div>
                    @endif

                    @if ($socials->isNotEmpty())
                        <div class="box">
                            <div class="box-h">{!! $use('link') !!}{{ $ui['follow'] }}</div>
                            <div class="socials">
                                @foreach ($socials as $k => $url)
                                    <a class="btn soft" href="{{ $url }}" target="_blank" rel="noopener">{{ config("xmenu.socials.$k") }}{!! $use('external') !!}</a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </section>

        {{-- PAY --}}
        <section class="tab" id="tab-pay" data-tab="pay">
            <div class="pane">
                <div class="pane-head"><h2>{{ $ui['pay_title'] }}</h2>@if ($cafe->paymentAccounts->isEmpty())<p>{{ $ui['no_pay'] }}</p>@endif</div>
                @if ($cafe->paymentAccounts->isNotEmpty())
                    <div class="boxes">
                        <div class="box wide">
                            @foreach ($cafe->paymentAccounts as $a)
                                @php $pc = $payColors[$a->type] ?? '#475569'; @endphp
                                <div class="line">
                                    <span class="pb" style="background:linear-gradient(150deg,{{ $pc }},{{ mix_hex($pc, '#000000', .3) }})">{{ $a->monogram() }}</span>
                                    <div class="t">
                                        <small>{{ $a->label() }}</small>
                                        <b class="mono">{{ $a->account_number }}</b>
                                        @if ($a->account_name)<small>{{ $a->account_name }}</small>@endif
                                    </div>
                                    <button class="btn" data-copy="{{ preg_replace('/\s+/', '', $a->account_number) }}" data-ev="pay_copy" data-v="{{ $a->type }}">{!! $use('copy') !!}<span>{{ $ui['copy'] }}</span></button>
                                </div>
                            @endforeach
                        </div>
                        <div class="note wide">{!! $use('receipt') !!}<span>{{ $ui['pay_note'] }}</span></div>
                    </div>
                @endif
            </div>
        </section>
    </main>

    <nav class="tabbar glass" id="tabbar" aria-label="Sections">
        <button type="button" class="on" data-to="menu">{!! $use('book') !!}{{ $ui['menu'] }}</button>
        @if ($hasGames)<button type="button" data-to="games">{!! $use('game') !!}{{ $ui['games'] }}</button>@endif
        <button type="button" data-to="info">{!! $use('info') !!}{{ $ui['info'] }}</button>
        <button type="button" data-to="pay">{!! $use('card') !!}{{ $ui['pay'] }}</button>
    </nav>
</div>

<div class="scrim" id="scrim"></div>
<div class="sheet" id="sheet" role="dialog" aria-modal="true" aria-labelledby="s-title">
    <div class="grab"><i></i></div>
    <button class="close glass" id="s-close" aria-label="{{ $ui['close'] }}">{!! $use('x', 's') !!}</button>
    <img class="simg" id="s-img" alt="">
    <div class="sart" id="s-art" hidden></div>
    <div class="sbody" id="s-body">
        <h3 id="s-title"></h3>
        <span class="price" id="s-price"></span>
        <div class="tags" id="s-tags"></div>
        <div class="gmeta" id="s-gmeta" hidden></div>
        <p class="sdesc" id="s-desc"></p>
        <div class="opts" id="s-var"><h4>{{ $ui['sizes'] }}</h4></div>
        <div class="opts" id="s-add"><h4>{{ $ui['addons'] }}</h4></div>
        <button class="btn big" id="s-play" hidden style="margin-top:22px">{!! $use('play') !!}{{ $ui['play'] }}</button>
        <button class="btn big soft back" id="s-back" type="button">{!! $use('left') !!}{{ $ui['back'] }}</button>
    </div>
</div>

<div class="player" id="player">
    <div class="load" id="p-load"></div>
    <button class="pclose" id="p-close">{!! $use('x', 's') !!}{{ $ui['close'] }}</button>
    <iframe id="p-frame" title="Game" sandbox="allow-scripts allow-same-origin" allow="fullscreen; autoplay"></iframe>
</div>

<script>
(function(){
var D={!! json_encode($sheetData, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) !!},
    G={!! json_encode($gameData, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) !!},
    GA={!! json_encode($games->mapWithKeys(fn ($g) => [$g->id => $g->artSvg()]), JSON_HEX_TAG | JSON_HEX_AMP) !!},
    H={!! json_encode($hours) !!},
    U={!! json_encode(['open' => $ui['open_now'], 'closed' => $ui['closed_now'], 'copied' => $ui['copied'], 'sold' => $ui['sold_out'], 'from' => $lang === 'am' ? 'ከ' : 'from', 'p1' => $ui['players_1'], 'p2' => $ui['players_2'], 'ai' => $lang === 'am' ? 'ከኮምፒውተር ጋር' : 'Play vs computer'], JSON_UNESCAPED_UNICODE) !!},
    CAFE={{ $cafe->id }}, CUR={!! json_encode($cur) !!}, PREVIEW={{ $preview ? 'true' : 'false' }}, LANG={!! json_encode($lang) !!},
    TRACK={!! json_encode(route('track')) !!};
var $=function(s,r){return (r||document).querySelector(s)}, $$=function(s,r){return Array.prototype.slice.call((r||document).querySelectorAll(s))};
function ic(id){return '<svg class="ic" aria-hidden="true"><use href="#i-'+id+'"/></svg>'}
var T=new URLSearchParams(location.search).get('t')||'';
try{ if(T) sessionStorage.setItem('xm_t',T); else T=sessionStorage.getItem('xm_t')||''; }catch(e){}

function ev(type,o){ if(PREVIEW) return; o=o||{}; var f=new FormData(); f.append('c',CAFE); f.append('type',type); if(T) f.append('t',T);
  for(var k in o) if(o[k]!=null) f.append(k,o[k]);
  try{ navigator.sendBeacon ? navigator.sendBeacon(TRACK,f) : fetch(TRACK,{method:'POST',body:f,keepalive:true}); }catch(e){} }
ev('menu_view');
@if ($hasGames)
// Cache the games in the background so they open instantly, even if the Wi-Fi drops.
if('serviceWorker' in navigator && !PREVIEW){ window.addEventListener('load',function(){ setTimeout(function(){ try{ navigator.serviceWorker.register({!! json_encode(asset('games/sw.js')) !!},{scope:{!! json_encode(asset('games').'/') !!}}); }catch(e){} },1500); }); }
@endif

$$('[data-lang]').forEach(function(a){ a.addEventListener('click',function(e){ e.preventDefault(); ev('language_switch',{v:a.dataset.lang});
  var p=new URLSearchParams(location.search); p.set('lang',a.dataset.lang); if(T) p.set('t',T); location.search=p.toString(); }); });

// Open / closed now
(function(){ var el=$('#status'); if(!el) return; var now=new Date(), d=now.getDay(), h=H[d]; var tr=$('#hours tr[data-day="'+d+'"]'); if(tr) tr.className='today';
  if(!h) return; var m=now.getHours()*60+now.getMinutes(), open=false;
  function mins(s){ if(!s) return null; var p=s.split(':'); return +p[0]*60 + +p[1]; }
  var o=mins(h.o), c=mins(h.c);
  if(!h.x && o!=null && c!=null){ open = c>o ? (m>=o && m<c) : (m>=o || m<c); }
  var y=H[(d+6)%7]; if(!open && y && !y.x){ var yo=mins(y.o), yc=mins(y.c); if(yo!=null && yc!=null && yc<yo && m<yc) open=true; }
  el.hidden=false; el.className='pill status '+(open?'open':'closed'); el.lastChild.textContent=open?U.open:U.closed; })();

// Tabs
var tabs=$$('#tabbar button');
function show(name,push){ $$('.tab').forEach(function(t){ t.classList.toggle('on',t.dataset.tab===name); });
  tabs.forEach(function(b){ b.classList.toggle('on',b.dataset.to===name); });
  var hero=$('#hero'); window.scrollTo({top: name==='menu' ? 0 : Math.max(0, hero.offsetTop + hero.offsetHeight + (innerWidth >= 900 ? -70 : 0)), behavior:'smooth'});
  if(push!==false) history.replaceState(history.state,'',name==='menu'?location.pathname+location.search:'#'+name); }
tabs.forEach(function(b){ b.addEventListener('click',function(){ show(b.dataset.to); }); });
function fromHash(){ var h=location.hash.slice(1); if(h && $('[data-tab="'+h+'"]')) show(h,false); }
fromHash(); window.addEventListener('hashchange',fromHash);

// Sticky category bar & active chip
var bar=$('#bar'), chips=$$('#chips .chip'), cats=$$('.cat');
function barOffset(){ return (parseFloat(getComputedStyle(bar).top)||0) + bar.offsetHeight + 8; }
if(bar && 'IntersectionObserver' in window){
  var sentinel=document.createElement('div'); bar.parentNode.insertBefore(sentinel,bar);
  new IntersectionObserver(function(es){ bar.classList.toggle('stuck', !es[0].isIntersecting); },{rootMargin:'-'+((parseFloat(getComputedStyle(bar).top)||0)+1)+'px 0px 0px 0px'}).observe(sentinel);
  var lock=0;
  var io=new IntersectionObserver(function(es){ if(Date.now()<lock) return; es.forEach(function(e){ if(e.isIntersecting) setChip(e.target.id); }); },{rootMargin:'-'+barOffset()+'px 0px -60% 0px'});
  cats.forEach(function(c){ io.observe(c); });
  chips.forEach(function(ch){ ch.addEventListener('click',function(e){ e.preventDefault(); var s=document.getElementById(ch.dataset.cat); if(!s) return;
    lock=Date.now()+900; setChip(ch.dataset.cat); window.scrollTo({top:s.getBoundingClientRect().top + window.pageYOffset - barOffset(), behavior:'smooth'}); ev('category_view'); }); });
}
function setChip(id){ chips.forEach(function(c){ var on=c.dataset.cat===id; c.classList.toggle('on',on); if(on && c.parentNode.scrollWidth>c.parentNode.clientWidth){ var p=c.parentNode; p.scrollTo({left:c.offsetLeft - p.clientWidth/2 + c.offsetWidth/2, behavior:'smooth'}); } }); }

// Search & tag filters
var q=$('#q'), sbox=$('#search'), active={}, items=$$('.item'), none=$('#none'), st;
function apply(){ var term=(q?q.value:'').trim().toLowerCase(), keys=Object.keys(active).filter(function(k){return active[k]}), any=0;
  items.forEach(function(it){ var ok=(!term || it.dataset.s.indexOf(term)>-1);
    if(ok && keys.length){ var t=' '+it.dataset.tags+' '; ok=keys.every(function(k){ return t.indexOf(' '+k+' ')>-1; }); }
    it.classList.toggle('hidden',!ok); if(ok) any++; });
  cats.forEach(function(c){ c.classList.toggle('hidden', !$('.item:not(.hidden)',c)); });
  if(none) none.classList.toggle('hidden', any>0);
  var pop=$('#pop'); if(pop) pop.classList.toggle('hidden', !!term || keys.length>0);
  if(sbox) sbox.classList.toggle('has', !!term); }
if(q){ q.addEventListener('input',function(){ apply(); clearTimeout(st); st=setTimeout(function(){ if(q.value.trim().length>1) ev('search',{v:q.value.trim().slice(0,60)}); },1200); });
  $('#qx').addEventListener('click',function(){ q.value=''; apply(); q.focus(); }); }
$$('[data-tag]').forEach(function(b){ b.addEventListener('click',function(){ var k=b.dataset.tag; active[k]=!active[k]; b.classList.toggle('on',active[k]); b.setAttribute('aria-pressed',active[k]); apply(); if(active[k]) ev('filter_used',{v:k}); }); });

// Sheet (items and game details)
var sheet=$('#sheet'), scrim=$('#scrim'), open=false, sheetGame=null;
function esc(s){ var d=document.createElement('div'); d.textContent=s==null?'':s; return d.innerHTML; }
function rows(list){ return list.map(function(r){ return '<div class="opt"><span>'+esc(r[0])+'</span><b>'+esc(r[1])+' '+esc(CUR)+'</b></div>'; }).join(''); }
function openSheet(){ sheet.scrollTop=0; sheet.classList.add('on'); scrim.classList.add('on'); document.body.style.overflow='hidden';
  if(!open){ history.pushState({sheet:1},''); open=true; } $('#s-close').focus({preventScroll:true}); }
function resetSheet(){ ['s-var','s-add'].forEach(function(id){ $('#'+id).style.display='none'; }); $('#s-gmeta').hidden=true; $('#s-play').hidden=true; $('#s-back').hidden=false; $('#s-art').hidden=true; $('#s-tags').innerHTML=''; $('#s-price').innerHTML=''; }
function openItem(id){ var d=D[id]; if(!d) return; resetSheet(); sheetGame=null;
  var img=$('#s-img'); if(d.i){ img.src=d.i2; img.srcset=d.i2+' 640w, '+d.i+' 1024w'; img.sizes='(min-width:900px) 560px, 100vw'; img.alt=d.n; img.style.display=''; } else { img.removeAttribute('src'); img.removeAttribute('srcset'); img.style.display='none'; }
  $('#s-body').classList.toggle('pad-top',!d.i);
  $('#s-title').textContent=d.n;
  var minV=d.v.length?d.v.reduce(function(m,v){return Math.min(m,parseFloat(String(v[1]).replace(/,/g,'')))},Infinity).toLocaleString():null;
  $('#s-price').innerHTML=(d.s?'<span class="sold-badge" style="margin:0 8px 0 0">'+esc(U.sold)+'</span>':'')+(minV?'<span class="from">'+esc(U.from)+'</span>'+esc(minV):esc(d.p))+' <small>'+esc(CUR)+'</small>';
  $('#s-tags').innerHTML=d.t.map(function(t){return '<span>'+ic(t[0])+esc(t[1])+'</span>'}).join('');
  $('#s-desc').textContent=d.d||'';
  var v=$('#s-var'), a=$('#s-add');
  v.style.display=d.v.length?'':'none'; v.innerHTML=v.firstElementChild.outerHTML+rows(d.v);
  a.style.display=d.a.length?'':'none'; a.innerHTML=a.firstElementChild.outerHTML+rows(d.a);
  openSheet(); ev('item_open',{item:id}); }
function openGameSheet(id){ var g=G[id]; if(!g) return; resetSheet(); sheetGame=id;
  $('#s-img').style.display='none'; var art=$('#s-art'); art.hidden=false; art.innerHTML=GA[id]||''; art.style.background=g.c;
  $('#s-body').classList.remove('pad-top');
  $('#s-title').textContent=g.n; $('#s-desc').textContent=g.d||'';
  var gm=$('#s-gmeta'); gm.hidden=false; gm.innerHTML='<span>'+ic('users')+esc(g.p==2?U.p2:U.p1)+'</span>'+(g.m?'<span>'+ic('game')+esc(g.m)+'</span>':'')+(g.ai?'<span>'+ic('cpu')+esc(U.ai)+'</span>':'');
  $('#s-play').hidden=false; $('#s-back').hidden=true; openSheet(); }
function closeSheet(fromPop){ if(!open) return; open=false; sheet.classList.remove('on'); scrim.classList.remove('on'); document.body.style.overflow=''; if(!fromPop) history.back(); }
items.forEach(function(it){ it.addEventListener('click',function(){ openItem(it.dataset.id); }); });
$$('[data-open]').forEach(function(b){ b.addEventListener('click',function(){ openItem(b.dataset.open); }); });
$$('[data-game]').forEach(function(g){ g.addEventListener('click',function(){ openGameSheet(g.dataset.game); }); });
scrim.addEventListener('click',function(){ closeSheet(); });
$('#s-close').addEventListener('click',function(){ closeSheet(); });
$('#s-back').addEventListener('click',function(){ closeSheet(); });
var y0=null; sheet.addEventListener('touchstart',function(e){ y0=sheet.scrollTop<=0?e.touches[0].clientY:null; },{passive:true});
sheet.addEventListener('touchmove',function(e){ if(y0==null||innerWidth>=900) return; var dy=e.touches[0].clientY-y0; if(dy>0){ sheet.style.transition='none'; sheet.style.transform='translate(-50%,'+dy+'px)'; } },{passive:true});
sheet.addEventListener('touchend',function(e){ if(y0==null) return; var dy=e.changedTouches[0].clientY-y0; sheet.style.transition=''; sheet.style.transform=''; if(dy>90 && innerWidth<900) closeSheet(); y0=null; });

// Copy buttons
$$('[data-copy]').forEach(function(b){ b.addEventListener('click',function(){ var txt=b.dataset.copy, lbl=$('span',b), old=lbl?lbl.textContent:'';
  function done(){ b.classList.add('done'); if(lbl) lbl.textContent=U.copied; if(navigator.vibrate) navigator.vibrate(15); setTimeout(function(){ b.classList.remove('done'); if(lbl) lbl.textContent=old; },1600); }
  function fallback(){ var t=document.createElement('textarea'); t.value=txt; t.style.position='fixed'; t.style.opacity='0'; document.body.appendChild(t); t.select(); try{document.execCommand('copy')}catch(e){} t.remove(); done(); }
  if(navigator.clipboard && window.isSecureContext){ navigator.clipboard.writeText(txt).then(done,fallback); } else fallback(); }); });
$$('[data-ev]').forEach(function(b){ b.addEventListener('click',function(){ ev(b.dataset.ev,{v:b.dataset.v}); }); });

// Game player — hosted games use the Xmenu game kit, which asks us to close via postMessage.
var player=$('#player'), frame=$('#p-frame'), pl=$('#p-load'), gameId=null, t0=0, gopen=false;
$('#s-play').addEventListener('click',function(){ var id=sheetGame, g=G[id]; if(!g) return; closeSheet(); setTimeout(function(){ startGame(id); }, 60); });
function startGame(id){ var g=G[id]; gameId=id; t0=Date.now(); pl.style.transition='none'; pl.style.width='0';
  player.classList.toggle('ext',!g.h); player.classList.add('on'); document.body.style.overflow='hidden';
  requestAnimationFrame(function(){ pl.style.transition=''; pl.style.width='85%'; });
  frame.onload=function(){ pl.style.width='100%'; setTimeout(function(){ pl.style.width='0'; },300); };
  var u=g.u+(g.u.indexOf('?')>-1?'&':'?')+'lang='+LANG+'&c='+encodeURIComponent(getComputedStyle(document.documentElement).getPropertyValue('--brand').trim());
  frame.src=u; if(!gopen){ history.pushState({game:1},''); gopen=true; } ev('game_open',{game:id}); }
function closeGame(fromPop){ if(!gopen) return; gopen=false; player.classList.remove('on'); frame.src='about:blank'; document.body.style.overflow='';
  ev('game_close',{game:gameId,v:Math.round((Date.now()-t0)/1000)}); if(!fromPop) history.back(); }
$('#p-close').addEventListener('click',function(){ closeGame(); });
window.addEventListener('message',function(e){ if(e.source===frame.contentWindow && e.data && e.data.xmenu==='exit') closeGame(); });

window.addEventListener('popstate',function(){ if(open) closeSheet(true); if(gopen) closeGame(true); });
document.addEventListener('keydown',function(e){ if(e.key==='Escape'){ if(gopen) closeGame(); else closeSheet(); } });
})();
</script>
</body>
</html>
