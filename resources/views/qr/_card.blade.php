@php
    /** @var array $card  @var array $s  @var float $W  @var float $H (mm) */
    $k = $W / 105;                                  // scale from the A6 base design
    $u = fn ($v) => round($v * $k, 2).'mm';
    $pt = fn ($v) => round($v * $k, 2).'pt';
    $c = $s['color'];
    $ink = ink_on($c);
    $tc = $ink === '#111111' ? mix_hex($c, '#000000', 0.5) : $c;   // brand colour as text on white
    $soft = mix_hex($c, '#FFFFFF', 0.9);
    $muted = '#6B6B78';
    $d = $s['design'];
    $sans = "'DejaVu Sans','Segoe UI',Arial,sans-serif";
    $eth = "'Ethiopic','Noto Sans Ethiopic','Nyala','DejaVu Sans',sans-serif";
    $serif = "'DejaVu Serif',Georgia,serif";
    $am = $s['amharic'] && multilang() ? 'ምናሌውን ለማየት ይቃኙ' : null;
    $tableLabel = $card['table'] !== null ? (ctype_digit((string) $card['table']) ? 'TABLE '.$card['table'] : mb_strtoupper($card['table'])) : null;

    $logo = function (float $box, bool $onColor = false) use ($card, $u, $pt, $c, $ink) {
        if ($card['logo']) {
            $r = $card['logo_ratio'] ?: 1;
            [$w, $h] = $r > 1.8 ? [$box * 1.8, $box * 1.8 / $r] : [$box * $r, $box];
            $pad = $onColor ? 'background:#fff;padding:'.$u(1.6).';border-radius:'.$u(4).';' : '';

            return '<div style="display:inline-block;'.$pad.'"><img src="'.$card['logo'].'" style="width:'.$u($w).';height:'.$u($h).';display:block"></div>';
        }
        [$bg, $fg] = $onColor ? ['#FFFFFF', $c] : [$c, $ink];

        // Centre the letter with padding: dompdf doesn't centre text vertically via line-height.
        $fs = $box * 0.55;                       // font size in mm-ish units
        $padTop = ($box - $fs) / 2 - $fs * 0.04; // line box ≈ 1em; nudge up for the cap height

        return '<div style="display:inline-block;width:'.$u($box).';height:'.$u($box - $padTop).';padding-top:'.$u($padTop).';line-height:1;background:'.$bg.';color:'.$fg.';border-radius:'.$u($box * 0.28).';font-weight:bold;font-size:'.$u($fs).';text-align:center">'.e($card['initial']).'</div>';
    };
    $frameBorder = 1.1;
    $framePad = $d === 'wifi' ? 7 : 9;
    // Pick the Ethiopic font for any text written in Amharic (names, Wi-Fi, etc.).
    $ff = fn ($text, $default = null) => preg_match('/\p{Ethiopic}/u', (string) $text) ? $eth : ($default ?? $sans);
@endphp
@php
    // Framed designs use padding on the card (not margins on the frame): dompdf collapses margins through the card.
    $inset = in_array($d, ['classic', 'wifi'], true) ? 5 * $k : 0;
@endphp
<div style="width:{{ round($W - 2 * $inset, 2) }}mm;height:{{ round($H - 2 * $inset, 2) }}mm;padding:{{ round($inset, 2) }}mm;position:relative;overflow:hidden;font-family:{{ $sans }};text-align:center;color:#16161A;background:{{ $d === 'bold' ? $c : '#FFFFFF' }}">

@if ($d === 'classic' || $d === 'wifi')
    <div style="height:{{ round($H - (10 + 2 * $frameBorder + $framePad) * $k, 2) }}mm;border:{{ $u($frameBorder) }} solid {{ $c }};border-radius:{{ $u(5) }};padding-top:{{ $u($framePad) }}">
        {!! $logo($d === 'wifi' ? 16 : 19) !!}
        <div style="font-family:{{ $ff($card['name']) }};font-size:{{ $pt(15) }};font-weight:bold;margin-top:{{ $u(3) }};line-height:1.15;padding:0 {{ $u(6) }}"><span style="font-family:{{ $ff($card['name'], 'inherit') }}">{{ $card['name'] }}</span></div>
        @if ($card['tagline'])<div style="font-family:{{ $ff($card['tagline']) }};font-size:{{ $pt(7) }};color:{{ $muted }};margin-top:{{ $u(1) }};padding:0 {{ $u(6) }}">{{ $card['tagline'] }}</div>@endif
        <div style="margin-top:{{ $u($d === 'wifi' ? 3.5 : 5) }}">
            <span style="display:inline-block;background:{{ $c }};color:{{ $ink }};font-size:{{ $pt(7.5) }};font-weight:bold;letter-spacing:{{ $u(0.25) }};text-transform:uppercase;padding:{{ $u(1.5) }} {{ $u(4) }};border-radius:{{ $u(10) }}">{{ $s['headline'] }}</span>
        </div>
        @if ($am)<div style="font-family:{{ $eth }};font-size:{{ $pt(7) }};color:{{ $tc }};margin-top:{{ $u(1.6) }}">{{ $am }}</div>@endif
        <img src="{{ $card['qr'] }}" style="width:{{ $u($d === 'wifi' ? 44 : 52) }};height:{{ $u($d === 'wifi' ? 44 : 52) }};margin:{{ $u(3) }} auto 0;display:block">
        @if ($tableLabel)
            <div style="font-size:{{ $pt(12) }};font-weight:bold;color:{{ $tc }};letter-spacing:{{ $u(0.3) }};margin-top:{{ $u(2.5) }}">{{ $tableLabel }}</div>
        @elseif ($s['subline'])
            <div style="font-size:{{ $pt(7) }};color:{{ $muted }};margin-top:{{ $u(2.5) }}">{{ $s['subline'] }}</div>
        @endif
        @if ($d === 'wifi' && $card['wifi_name'])
            <div style="margin:{{ $u(3) }} {{ $u(9) }} 0;background:{{ $soft }};border-radius:{{ $u(3) }};padding:{{ $u(2.2) }} {{ $u(3) }};font-size:{{ $pt(7) }};text-align:left">
                <div><span style="color:{{ $muted }}">Wi-Fi</span>&nbsp;&nbsp;<b style="font-family:{{ $ff($card['wifi_name'], 'inherit') }}">{{ $card['wifi_name'] }}</b></div>
                @if ($card['wifi_password'])<div style="margin-top:{{ $u(0.8) }}"><span style="color:{{ $muted }}">Password</span>&nbsp;&nbsp;<b>{{ $card['wifi_password'] }}</b></div>@endif
            </div>
        @endif
        <div style="font-size:{{ $pt(5.8) }};color:#9A9AA6;margin-top:{{ $u(2.5) }}">{{ $card['url'] }}</div>
    </div>

@elseif ($d === 'bold')
    <div style="padding-top:{{ $u(11) }};color:{{ $ink }}">
        {!! $logo(17, true) !!}
        <div style="font-size:{{ $pt(14) }};font-weight:bold;margin-top:{{ $u(3) }};padding:0 {{ $u(8) }};line-height:1.15"><span style="font-family:{{ $ff($card['name'], 'inherit') }}">{{ $card['name'] }}</span></div>
        <div style="font-size:{{ $pt(17) }};font-weight:bold;margin-top:{{ $u(6) }};line-height:1.1;padding:0 {{ $u(8) }}">{{ $s['headline'] }}</div>
        @if ($am)<div style="font-family:{{ $eth }};font-size:{{ $pt(7.5) }};margin-top:{{ $u(1.5) }};opacity:.9">{{ $am }}</div>@endif
        <div style="background:#FFFFFF;border-radius:{{ $u(5) }};width:{{ $u(56) }};padding:{{ $u(4) }};margin:{{ $u(5) }} auto 0">
            <img src="{{ $card['qr'] }}" style="width:{{ $u(56) }};height:{{ $u(56) }};display:block">
        </div>
        @if ($tableLabel)
            <div style="margin-top:{{ $u(4.5) }}"><span style="display:inline-block;background:#FFFFFF;color:{{ $tc }};font-weight:bold;font-size:{{ $pt(10) }};letter-spacing:{{ $u(0.3) }};padding:{{ $u(1.5) }} {{ $u(5) }};border-radius:{{ $u(10) }}">{{ $tableLabel }}</span></div>
        @elseif ($s['subline'])
            <div style="font-size:{{ $pt(7.5) }};margin-top:{{ $u(4.5) }};opacity:.9">{{ $s['subline'] }}</div>
        @endif
        <div style="font-size:{{ $pt(5.8) }};margin-top:{{ $u(3) }};opacity:.75">{{ $card['url'] }}</div>
    </div>

@elseif ($d === 'split')
    <div style="height:{{ round($H * 0.4 - 10 * $k, 2) }}mm;background:{{ $c }};color:{{ $ink }};padding-top:{{ $u(10) }}">
        {!! $logo(19, true) !!}
        <div style="font-size:{{ $pt(15) }};font-weight:bold;margin-top:{{ $u(3.5) }};padding:0 {{ $u(8) }};line-height:1.15"><span style="font-family:{{ $ff($card['name'], 'inherit') }}">{{ $card['name'] }}</span></div>
        @if ($card['tagline'])<div style="font-size:{{ $pt(7) }};margin-top:{{ $u(1) }};opacity:.85;padding:0 {{ $u(8) }}">{{ $card['tagline'] }}</div>@endif
    </div>
    <div style="padding-top:{{ $u(6) }}">
        <div style="font-size:{{ $pt(9) }};font-weight:bold;letter-spacing:{{ $u(0.35) }};text-transform:uppercase;color:{{ $tc }}">{{ $s['headline'] }}</div>
        @if ($am)<div style="font-family:{{ $eth }};font-size:{{ $pt(7) }};color:{{ $muted }};margin-top:{{ $u(1) }}">{{ $am }}</div>@endif
        <img src="{{ $card['qr'] }}" style="width:{{ $u(50) }};height:{{ $u(50) }};margin:{{ $u(3.5) }} auto 0;display:block">
        @if ($tableLabel)
            <div style="font-size:{{ $pt(11) }};font-weight:bold;color:{{ $tc }};letter-spacing:{{ $u(0.3) }};margin-top:{{ $u(2.5) }}">{{ $tableLabel }}</div>
        @elseif ($s['subline'])
            <div style="font-size:{{ $pt(7) }};color:{{ $muted }};margin-top:{{ $u(2.5) }}">{{ $s['subline'] }}</div>
        @endif
        <div style="font-size:{{ $pt(5.8) }};color:#9A9AA6;margin-top:{{ $u(2) }}">{{ $card['url'] }}</div>
    </div>

@else {{-- minimal --}}
    <div style="padding-top:{{ $u(12) }}">
        {!! $logo(13) !!}
        <div style="font-family:{{ $serif }};font-size:{{ $pt(17) }};margin-top:{{ $u(4) }};padding:0 {{ $u(10) }};line-height:1.15"><span style="font-family:{{ $ff($card['name'], 'inherit') }}">{{ $card['name'] }}</span></div>
        <div style="width:{{ $u(16) }};height:{{ $u(0.5) }};background:{{ $c }};margin:{{ $u(4) }} auto 0"></div>
        <img src="{{ $card['qr'] }}" style="width:{{ $u(60) }};height:{{ $u(60) }};margin:{{ $u(7) }} auto 0;display:block">
        <div style="font-size:{{ $pt(7) }};letter-spacing:{{ $u(0.6) }};text-transform:uppercase;color:{{ $muted }};margin-top:{{ $u(5) }}">{{ $s['headline'] }}</div>
        @if ($am)<div style="font-family:{{ $eth }};font-size:{{ $pt(6.5) }};color:{{ $muted }};margin-top:{{ $u(1) }}">{{ $am }}</div>@endif
        @if ($tableLabel)<div style="font-family:{{ $serif }};font-size:{{ $pt(12) }};color:{{ $tc }};margin-top:{{ $u(3) }}">{{ $tableLabel }}</div>@endif
    </div>
    <div style="position:absolute;left:0;right:0;bottom:{{ $u(7) }};font-size:{{ $pt(5.8) }};color:#9A9AA6">{{ $card['url'] }}</div>
@endif
</div>
