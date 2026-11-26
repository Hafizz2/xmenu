{{-- Shared error page. Self-contained (inline CSS, no database, no auth) so it still renders when things are broken. --}}
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta name="robots" content="noindex">
<meta name="theme-color" content="#FBF8F4">
<title>@yield('title') · Xmenu</title>
<link rel="icon" href="/favicon.svg" type="image/svg+xml">
<style>
*{box-sizing:border-box}
html,body{margin:0;height:100%}
body{font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Noto Sans Ethiopic","Helvetica Neue",Arial,sans-serif;background:#FBF8F4;color:#1B1A1F;-webkit-font-smoothing:antialiased;display:flex;align-items:center;justify-content:center;padding:24px}
.box{max-width:440px;width:100%;text-align:center}
.code{display:inline-flex;align-items:center;gap:8px;height:30px;padding:0 13px;border-radius:99px;background:#fff;box-shadow:inset 0 0 0 1px rgba(30,20,10,.1);font-size:13px;font-weight:650;color:#6E6A73;letter-spacing:.02em}
.code i{width:8px;height:8px;border-radius:50%;background:#C2410C}
.ic{width:88px;height:88px;margin:26px auto 22px;border-radius:26px;background:#FFF1E8;color:#C2410C;display:flex;align-items:center;justify-content:center}
.ic svg{width:42px;height:42px;fill:none;stroke:currentColor;stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round}
h1{font-size:30px;line-height:1.15;letter-spacing:-.025em;margin:0 0 10px;font-weight:780}
p{font-size:16.5px;line-height:1.55;color:#6E6A73;margin:0 auto;max-width:380px}
.am{margin-top:8px;font-size:15px}
.btns{display:flex;flex-direction:column;gap:10px;margin-top:28px}
.btn{height:52px;border-radius:99px;display:flex;align-items:center;justify-content:center;gap:8px;font-weight:650;font-size:16px;text-decoration:none;border:0;cursor:pointer;font-family:inherit}
.btn.primary{background:#C2410C;color:#fff;box-shadow:0 8px 20px -10px #C2410C}
.btn.soft{background:#fff;color:#1B1A1F;box-shadow:inset 0 0 0 1px rgba(30,20,10,.12)}
.foot{margin-top:34px;font-size:13px;color:#A19DA6}
.foot a{color:inherit}
</style>
</head>
<body>
<main class="box">
    <span class="code"><i></i>@yield('code')</span>
    <div class="ic">@yield('icon')</div>
    <h1>@yield('heading')</h1>
    <p>@yield('message')</p>
    @hasSection('message_am')<p class="am" lang="am">@yield('message_am')</p>@endif
    <div class="btns">
        @section('buttons')
            <a class="btn primary" href="/">Go to the home page</a>
            <a class="btn soft" href="javascript:history.back()">Go back</a>
        @show
    </div>
    <div class="foot">Xmenu · <a href="/">xmenu.xvalue.et</a></div>
</main>
</body>
</html>
