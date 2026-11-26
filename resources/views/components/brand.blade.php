@props(['href' => null, 'tag' => null])
<a href="{{ $href ?? route('home') }}" {{ $attributes->merge(['class' => 'brandmark']) }} aria-label="Xmenu home">
    <span class="x" aria-hidden="true">X</span><span>menu</span>
    @if ($tag)<span class="admin-tag">{{ $tag }}</span>@endif
</a>
