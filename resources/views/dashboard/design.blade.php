@extends('layouts.panel')
@section('title', 'Look & colours')

@section('content')
<div class="page-head">
    <div>
        <h1>Look &amp; colours</h1>
        <p>Your colours on our professional layout. Changes show in the preview right away.</p>
    </div>
</div>

<form method="post" id="design-form">
    @csrf @method('put')
    <input type="hidden" name="color" id="color" value="{{ $theme['color'] }}">

    <div class="studio">
        <div class="stack">
            <div class="card card-pad">
                <div class="opt-group">
                    <div class="label">Brand colour</div>
                    <div class="swatches">
                        @foreach (config('xmenu.palette') as $hex => $name)
                            <label class="swatch" title="{{ $name }}"><input type="radio" name="_swatch" value="{{ $hex }}" @checked(strtoupper($theme['color']) === $hex)><span style="background:{{ $hex }}"></span></label>
                        @endforeach
                        <label class="swatch" title="Custom colour" style="position:relative">
                            <input type="color" class="color-input" id="color-custom" value="{{ $theme['color'] }}" style="position:static;opacity:1">
                        </label>
                    </div>
                    <p class="hint">Tip: match the colour of your logo or your sign. Text colours adjust automatically so they stay readable.</p>
                </div>

                <div class="opt-group">
                    <div class="label">Menu layout</div>
                    <div class="tiles">
                        @foreach (config('xmenu.layouts') as $k => $l)
                            <label class="tile"><input type="radio" name="layout" value="{{ $k }}" @checked($theme['layout'] === $k)>
                                <span class="tile-in">
                                    <span class="design-thumb" style="background:#F6F5F2;padding:8px;display:block">
                                        @if ($k === 'list')
                                            @for ($i = 0; $i < 3; $i++)<span style="display:flex;gap:5px;align-items:center;margin-bottom:5px"><span style="flex:1"><i style="display:block;height:5px;width:70%;background:#C9C5BD;border-radius:3px"></i><i style="display:block;height:4px;width:40%;background:#DDD9D2;border-radius:3px;margin-top:3px"></i></span><i style="width:18px;height:18px;border-radius:4px;background:var(--sw,#D9A07F)"></i></span>@endfor
                                        @elseif ($k === 'grid')
                                            <span style="display:grid;grid-template-columns:1fr 1fr;gap:5px">@for ($i = 0; $i < 4; $i++)<span style="background:#fff;border-radius:5px;padding:3px"><i style="display:block;height:20px;border-radius:3px;background:#D9A07F"></i><i style="display:block;height:4px;width:70%;background:#C9C5BD;border-radius:3px;margin-top:3px"></i></span>@endfor</span>
                                        @else
                                            <span style="display:block;background:#fff;border-radius:6px;padding:4px"><i style="display:block;height:42px;border-radius:4px;background:#D9A07F"></i><i style="display:block;height:5px;width:60%;background:#C9C5BD;border-radius:3px;margin-top:5px"></i><i style="display:block;height:4px;width:35%;background:#DDD9D2;border-radius:3px;margin-top:3px"></i></span>
                                        @endif
                                    </span>
                                    <b>{{ $l['label'] }}</b><small>{{ $l['hint'] }}</small>
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="opt-group">
                    <div class="label">Header</div>
                    <div class="tiles">
                        @foreach (config('xmenu.headers') as $k => $l)
                            <label class="tile"><input type="radio" name="header" value="{{ $k }}" @checked($theme['header'] === $k)>
                                <span class="tile-in">
                                    <span class="design-thumb" data-header-thumb="{{ $k }}" style="background:{{ $k === 'cover' ? 'linear-gradient(135deg,#8B5E3C,#3E2A1E)' : ($k === 'solid' ? 'var(--sel)' : '#F6F5F2') }};flex-direction:column;gap:5px">
                                        <i style="width:22px;height:22px;border-radius:7px;background:{{ $k === 'minimal' ? 'var(--sel)' : '#fff' }}"></i>
                                        <i style="width:46px;height:5px;border-radius:3px;background:{{ $k === 'minimal' ? '#9A958C' : 'rgba(255,255,255,.85)' }}"></i>
                                    </span>
                                    <b>{{ $l['label'] }}</b><small>{{ $l['hint'] }}</small>
                                </span>
                            </label>
                        @endforeach
                    </div>
                    @if (! $cafe->cover)<p class="hint">No cover photo yet. <a href="{{ route('dashboard.info') }}">Add one in Café info</a>, or we’ll use your colour.</p>@endif
                </div>

                <div class="opt-group">
                    <div class="label">Font</div>
                    <div class="tiles" style="grid-template-columns:repeat(auto-fill,minmax(110px,1fr))">
                        @foreach (config('xmenu.fonts') as $k => $f)
                            <label class="tile"><input type="radio" name="font" value="{{ $k }}" @checked($theme['font'] === $k)>
                                <span class="tile-in" style="text-align:center"><span style="display:block;font:600 30px/1.1 {{ $f['stack'] }};margin:4px 0 6px">Aa</span><b>{{ $f['label'] }}</b></span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="grid-2 opt-group">
                    <div>
                        <div class="label">Corners</div>
                        <div class="seg">
                            @foreach (config('xmenu.radii') as $k => $r)
                                <label><input type="radio" name="radius" value="{{ $k }}" @checked($theme['radius'] === $k)><span><i style="width:14px;height:14px;border:2px solid currentColor;border-radius:{{ max(1, $r['px'] / 3) }}px;display:inline-block"></i>{{ $r['label'] }}</span></label>
                            @endforeach
                        </div>
                    </div>
                    <div>
                        <div class="label">Light or dark</div>
                        <div class="seg">
                            @foreach (['light' => 'Light', 'auto' => 'Auto', 'dark' => 'Dark'] as $k => $l)
                                <label><input type="radio" name="mode" value="{{ $k }}" @checked($theme['mode'] === $k)><span>{{ $l }}</span></label>
                            @endforeach
                        </div>
                        <p class="hint">Light is easiest to read for most customers. Auto follows the customer’s phone setting.</p>
                    </div>
                </div>

                <div class="opt-group">
                    <div class="label">Background</div>
                    <div class="seg">
                        @foreach (config('xmenu.surfaces') as $k => $s)
                            <label><input type="radio" name="surface" value="{{ $k }}" @checked($theme['surface'] === $k)><span><i style="width:14px;height:14px;border-radius:50%;background:{{ $s['bg'] }};box-shadow:inset 0 0 0 1px rgba(0,0,0,.15);display:inline-block"></i>{{ $s['label'] }}</span></label>
                        @endforeach
                    </div>
                </div>

                <div class="opt-group">
                    <div class="label">What customers see</div>
                    <p class="hint" style="margin:-4px 0 12px">Turn off anything you don’t need. Fewer things on screen makes the menu even simpler.</p>
                    <div class="stack-lg">
                        @foreach (config('xmenu.theme_switches') as $k => [$label, $hint])
                            <label class="switch">
                                <input type="hidden" name="{{ $k }}" value="0">
                                <input type="checkbox" name="{{ $k }}" value="1" @checked($theme[$k])>
                                <span class="track"></span>
                                <span><b>{{ $label }}</b><br><span class="small muted">{{ $hint }}@if ($k === 'show_wifi' && ! $cafe->wifi_name) <a href="{{ route('dashboard.info') }}">Add your Wi-Fi first</a>.@endif</span></span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="preview-col">
            <div class="phone"><iframe id="preview" title="Menu preview" src="{{ route('dashboard.preview', collect($theme)->only(['color', 'layout', 'header', 'font', 'radius', 'mode', 'surface'])->all() + collect(config('xmenu.theme_switches'))->mapWithKeys(fn ($v, $k) => [$k => $theme[$k] ? '1' : '0'])->all()) }}"></iframe></div>
            <div class="row mt-4" style="justify-content:center">
                <a href="{{ $cafe->menuUrl() }}" target="_blank" class="btn btn-sm btn-ghost"><x-icon name="external" class="sm"/>Open live menu</a>
            </div>
        </div>
    </div>

    <div class="sticky-save">
        <span class="small muted grow" id="dirty-note" style="align-self:center;display:none">You have unpublished changes.</span>
        <button class="btn btn-primary"><x-icon name="sparkle"/>Publish design</button>
    </div>
</form>
@endsection

@push('scripts')
<script>
(function () {
    const form = document.getElementById('design-form');
    const frame = document.getElementById('preview');
    const color = document.getElementById('color');
    const custom = document.getElementById('color-custom');
    const note = document.getElementById('dirty-note');
    const base = @json(route('dashboard.preview'));
    let t;
    function paintThumbs() { form.style.setProperty('--sel', color.value); }
    function refresh() {
        paintThumbs();
        note.style.display = '';
        clearTimeout(t);
        t = setTimeout(() => {
            const fd = new FormData(form);
            const p = new URLSearchParams();
            ['color', 'layout', 'header', 'font', 'radius', 'mode', 'surface'].forEach(k => p.set(k, fd.get(k)));
            @json(array_keys(config('xmenu.theme_switches'))).forEach(k => p.set(k, fd.getAll(k).pop()));
            frame.src = base + '?' + p.toString();
        }, 200);
    }
    form.addEventListener('change', e => {
        if (e.target.name === '_swatch') { color.value = e.target.value; custom.value = e.target.value; }
        refresh();
    });
    custom.addEventListener('input', () => {
        color.value = custom.value.toUpperCase();
        form.querySelectorAll('[name=_swatch]').forEach(r => (r.checked = false));
        refresh();
    });
    paintThumbs();
})();
</script>
@endpush
