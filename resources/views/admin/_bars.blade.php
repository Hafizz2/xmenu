@php $max = max(1, collect($data)->max('n')); $fmt = $money ?? false; @endphp
<div class="bars" role="img" aria-label="{{ $label ?? 'Chart' }}" style="height:{{ $height ?? 140 }}px;gap:{{ count($data) > 40 ? 2 : (count($data) > 20 ? 3 : 6) }}px">
    @foreach ($data as $d)
        <div class="b {{ $d['n'] ? '' : 'zero' }}" title="{{ $d['label'] }}: {{ $fmt ? number_format($d['n']) : $d['n'] }}">
            <i style="height:{{ max(2, round($d['n'] / $max * 100)) }}%;max-width:none"></i>
            <span>{{ count($data) <= 31 || $loop->index % 7 === 0 ? $d['short'] : '' }}</span>
        </div>
    @endforeach
</div>
