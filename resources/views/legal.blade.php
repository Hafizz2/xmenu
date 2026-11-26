@php
    $titles = ['privacy' => 'Privacy', 'terms' => 'Terms of use', 'credits' => 'Photo credits'];
    $support = \App\Models\Setting::get('support', []);
    $contact = collect([$support['email'] ?? null, $support['phone'] ?? null, ! empty($support['telegram']) ? 'Telegram @'.ltrim($support['telegram'], '@') : null])->filter()->implode(' · ');
@endphp
<!doctype html>
<html lang="en">
<head>
@include('partials.head')
<title>{{ $titles[$page] }} · Xmenu</title>
<meta name="description" content="{{ $titles[$page] }} for Xmenu, the QR menu service by Xvalue.">
<link rel="canonical" href="{{ route('legal', $page) }}">
<style>
body{background:#FBF8F4;color:#1B1A1F}
.top{max-width:760px;margin:0 auto;padding:22px 20px;display:flex;align-items:center;justify-content:space-between}
article{max-width:760px;margin:0 auto;padding:10px 20px 60px;font-size:16.5px;line-height:1.65}
article h1{font-size:clamp(32px,5vw,44px);letter-spacing:-.035em;font-weight:780;margin:18px 0 6px}
article .upd{color:#6E6A73;font-size:14.5px}
article h2{font-size:21px;font-weight:700;margin:34px 0 8px;letter-spacing:-.015em}
article p,article li{color:#3B3940}
article p{margin:10px 0}
article ul{padding-left:22px}
article table{width:100%;border-collapse:collapse;font-size:14px;margin-top:18px;background:#fff;border-radius:14px;overflow:hidden}
article th,article td{text-align:left;padding:10px 12px;border-bottom:1px solid rgba(30,20,10,.07);vertical-align:top}
article a{color:#C2410C}
.foot{max-width:760px;margin:0 auto;padding:0 20px 40px;color:#6E6A73;font-size:14px}
.foot a{color:#1B1A1F;font-weight:650;text-decoration:none}
</style>
</head>
<body>
<div class="top"><x-brand /><a href="{{ route('home') }}" class="btn btn-sm"><x-icon name="left" class="sm"/>Home</a></div>
<article>
    <h1>{{ $titles[$page] }}</h1>

    @if ($page === 'privacy')
        <p class="upd">Last updated {{ now()->format('j F Y') }}</p>
        <p>Xmenu is a service by Xvalue. This page explains, in plain words, what information we keep and why.</p>
        <h2>Café owners</h2>
        <p>When you sign up we keep your name, phone number, email address and a securely scrambled (hashed) version of your password. We also keep what you add to your menu: dishes, prices, photos, café details, Wi-Fi details you choose to show, and payment account numbers you choose to show.</p>
        <p>We use this only to run your menu, contact you about your account and process your subscription payments. We do not sell your information.</p>
        <h2>Customers who scan a menu</h2>
        <p>Customers do not sign up and we do not ask for their name or phone number. To show owners simple statistics, we count events such as “menu opened”, “dish opened” or “game played”, with the time and the table number from the QR code. We do not store customers’ IP addresses with these counts.</p>
        <p>The menu remembers the customer’s language choice in a small cookie, and games remember settings such as sound on the customer’s own phone.</p>
        <h2>Who can see your information</h2>
        <p>Your public menu is visible to anyone with the link. Your account details are visible only to you and to Xvalue staff who support the service. When our staff edit a menu on an owner’s behalf, the change is recorded in an audit log.</p>
        <h2>Keeping it safe</h2>
        <p>The site is served over an encrypted connection (HTTPS), passwords are never stored in readable form, and sign-in attempts are limited to stop password guessing.</p>
        <h2>Deleting your data</h2>
        <p>You can ask us to delete your café and account at any time. We remove your menu, photos and statistics, except records we must keep for accounting.</p>
        <h2>Contact</h2>
        <p>Questions about privacy: {{ $contact ?: 'contact us through xvalue.et' }}.</p>

    @elseif ($page === 'terms')
        <p class="upd">Last updated {{ now()->format('j F Y') }}</p>
        <p>These terms apply when you use Xmenu, a service by Xvalue. By creating an account you agree to them.</p>
        <h2>Your account</h2>
        <ul>
            <li>Give correct details and keep your password private. You are responsible for what is done with your account.</li>
            <li>One account is for one café or restaurant. Branches need their own café.</li>
        </ul>
        <h2>Your content</h2>
        <ul>
            <li>You own your menu, photos and logo. You give us permission to show them on your menu and in your QR codes.</li>
            <li>Only upload photos and logos you have the right to use. Prices and dish information are your responsibility.</li>
            <li>Do not use Xmenu for anything illegal, misleading or harmful. We may remove such content or suspend the account.</li>
        </ul>
        <h2>Free trial and payment</h2>
        <ul>
            <li>New cafés get a free trial. After it ends, the menu keeps working while you choose a plan.</li>
            <li>Plans are paid monthly in advance by Telebirr or bank transfer. Payments are confirmed by our team.</li>
            <li>If a plan is not renewed, we may pause the menu until payment is made. Your data is kept for a reasonable time.</li>
        </ul>
        <h2>The service</h2>
        <p>We work hard to keep Xmenu fast and available, but we cannot promise it will never be interrupted. We may improve or change features over time. We are not responsible for losses caused by wrong prices or information entered in a menu.</p>
        <h2>Ending</h2>
        <p>You can stop using Xmenu at any time. We may close accounts that break these terms.</p>
        <h2>Contact</h2>
        <p>{{ $contact ?: 'Contact us through xvalue.et' }}.</p>

    @else
        <p>The sample photos in the Xmenu demo café come from Wikimedia Commons under free licences. Thank you to the photographers.</p>
        {!! $credits !!}
    @endif
</article>
<div class="foot">© {{ date('Y') }} Xmenu · Xmenu by <a href="https://xvalue.et" target="_blank" rel="noopener">Xvalue</a></div>
</body>
</html>
