<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex">
<title>{{ $cafe->name }}</title>
<style>body{margin:0;min-height:100vh;display:flex;align-items:center;justify-content:center;font-family:system-ui,-apple-system,"Segoe UI",Roboto,"Noto Sans Ethiopic",sans-serif;background:#F6F5F2;color:#16161A;text-align:center;padding:24px}h1{font-size:22px;margin:12px 0 6px}p{color:#6B6B78;margin:4px 0}</style>
</head>
<body>
<div>
    <svg viewBox="0 0 24 24" width="44" height="44" fill="none" stroke="#6B6B78" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="display:block;margin:0 auto">{!! \App\Support\Icons::path('utensils') !!}</svg>
    <h1>{{ $cafe->name }}</h1>
    <p>This menu isn’t available right now. Please ask your waiter.</p>
    <p lang="am">ምናሌው አሁን አይገኝም። እባክዎ አስተናጋጁን ይጠይቁ።</p>
</div>
</body>
</html>
