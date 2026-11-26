<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
    html, body { margin: 0; padding: 0; background: transparent; overflow: hidden; }
    #wrap { transform-origin: 0 0; }
</style>
</head>
<body>
<div id="wrap">@include('qr._card', ['card' => $card, 's' => $s, 'W' => $paper['w'], 'H' => $paper['h']])</div>
<script>
    // Scale the real-size card to fill this frame.
    (function () {
        const w = document.getElementById('wrap');
        function fit() {
            const r = w.firstElementChild.getBoundingClientRect();
            const k = Math.min(innerWidth / (r.width / (w._k || 1)), innerHeight / (r.height / (w._k || 1)));
            w._k = k;
            w.style.transform = 'scale(' + k + ')';
        }
        fit(); addEventListener('resize', fit);
    })();
</script>
</body>
</html>
