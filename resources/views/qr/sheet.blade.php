<!doctype html>
<html>
<head>
<meta charset="utf-8">
<style>
    @page { margin: 0; }
    html, body { margin: 0; padding: 0; }
    table { border-collapse: collapse; }
    td { padding: 0; vertical-align: top; }
    .cut td { border-right: 0.2mm dashed #CFCFCF; border-bottom: 0.2mm dashed #CFCFCF; }
</style>
</head>
<body>
@foreach ($pages as $cards)
    <div style="width:{{ $page['w'] }}mm;height:{{ $page['h'] }}mm;overflow:hidden;{{ $loop->last ? '' : 'page-break-after:always;' }}">
        <table class="{{ $tile ? 'cut' : '' }}">
            @foreach (array_chunk($cards, $page['cols']) as $row)
                <tr>
                    @foreach ($row as $card)
                        <td>@include('qr._card', ['card' => $card, 's' => $s, 'W' => $paper['w'] - ($tile ? 0.3 : 0), 'H' => $paper['h'] - ($tile ? 0.6 : 0)])</td>
                    @endforeach
                </tr>
            @endforeach
        </table>
    </div>
@endforeach
</body>
</html>
