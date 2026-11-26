@extends('layouts.panel')
@section('title', 'QR codes')

@section('content')
<div class="page-head">
    <div>
        <h1>QR codes to print</h1>
        <p>Pick a design, choose your paper size and download a print-ready PDF.</p>
    </div>
</div>

<form id="qr-form" method="get" action="{{ route('dashboard.qr.pdf') }}">
    <div class="studio">
        <div class="card card-pad">
            <div class="opt-group">
                <div class="label">Design</div>
                <div class="tiles">
                    @foreach (config('xmenu.qr_designs') as $k => $d)
                        <label class="tile"><input type="radio" name="design" value="{{ $k }}" @checked($settings['design'] === $k)>
                            <span class="tile-in">
                                <span class="design-thumb" style="height:96px;background:#F1EFEB">
                                    @php $c = 'var(--qc)'; @endphp
                                    <span style="width:52px;height:74px;border-radius:3px;box-shadow:0 2px 6px rgba(0,0,0,.15);display:flex;flex-direction:column;align-items:center;overflow:hidden;
                                        background:{{ $k === 'bold' ? $c : '#fff' }};{{ in_array($k, ['classic', 'wifi']) ? 'outline:2px solid var(--qc);outline-offset:-5px;' : '' }}">
                                        @if ($k === 'split')<span style="width:100%;height:28px;background:{{ $c }};display:flex;align-items:center;justify-content:center"><i style="width:9px;height:9px;border-radius:3px;background:#fff"></i></span>
                                        @else<i style="width:9px;height:9px;border-radius:3px;margin-top:9px;background:{{ $k === 'bold' ? '#fff' : $c }}"></i>@endif
                                        <i style="width:24px;height:24px;margin-top:6px;background:#fff;border:{{ $k === 'bold' ? '3px solid #fff' : '0' }};background-image:linear-gradient(45deg,#222 25%,transparent 25%,transparent 75%,#222 75%),linear-gradient(45deg,#222 25%,transparent 25%,transparent 75%,#222 75%);background-size:6px 6px;background-position:0 0,3px 3px"></i>
                                        @if ($k === 'wifi')<i style="width:30px;height:6px;margin-top:4px;border-radius:2px;background:#eee"></i>@endif
                                    </span>
                                </span>
                                <b>{{ $d['label'] }}</b><small>{{ $d['hint'] }}</small>
                            </span>
                        </label>
                    @endforeach
                </div>
                @if (! $cafe->wifi_name)<p class="hint" id="wifi-hint" style="{{ $settings['design'] === 'wifi' ? '' : 'display:none' }}">Add your Wi-Fi in <a href="{{ route('dashboard.info') }}">Café info</a> to show it on this design.</p>@endif
            </div>

            <div class="grid-2 opt-group">
                <div>
                    <div class="label">Paper size</div>
                    <div class="seg">
                        @foreach (['A6' => 'Table card', 'A5' => 'Stand', 'A4' => 'Poster'] as $k => $l)
                            <label><input type="radio" name="size" value="{{ $k }}" @checked($settings['size'] === $k)><span>{{ $k }}</span></label>
                        @endforeach
                    </div>
                    <p class="hint">A6 (10.5 × 14.8 cm) fits table stands. A5 for counters. A4 for walls and windows.</p>
                </div>
                <div id="sheet-opt">
                    <div class="label">Printing</div>
                    <div class="seg">
                        <label><input type="radio" name="sheet" value="single" @checked($settings['sheet'] === 'single')><span>One per page</span></label>
                        <label><input type="radio" name="sheet" value="tile" @checked($settings['sheet'] === 'tile')><span id="tile-label">Save paper</span></label>
                    </div>
                    <p class="hint" id="tile-hint">Puts several cards on one A4 sheet with cut lines. Works on any home or office printer.</p>
                </div>
            </div>

            <div class="opt-group">
                <div class="label">Colour</div>
                <div class="row row-wrap" style="gap:10px">
                    <input type="color" class="color-input" name="color" value="{{ $settings['color'] }}" id="qr-color">
                    <button type="button" class="btn btn-sm" data-color="{{ $cafe->themeValue('color') }}"><i style="width:14px;height:14px;border-radius:50%;background:{{ $cafe->themeValue('color') }}"></i>Menu colour</button>
                    @foreach (['#111827', '#B45309', '#15803D', '#1D4ED8', '#BE123C'] as $hex)
                        <button type="button" class="btn btn-sm btn-icon" data-color="{{ $hex }}" aria-label="{{ $hex }}"><i style="width:18px;height:18px;border-radius:50%;background:{{ $hex }}"></i></button>
                    @endforeach
                </div>
            </div>

            <div class="grid-2 opt-group">
                <div class="field"><label>Headline</label><input class="input" name="headline" value="{{ $settings['headline'] }}" maxlength="40"></div>
                <div class="field"><label>Small text</label><input class="input" name="subline" value="{{ $settings['subline'] }}" maxlength="70"></div>
            </div>

            <div class="grid-2 opt-group">
                <div>
                    <div class="label">QR style</div>
                    <div class="seg">
                        <label><input type="radio" name="qr_style" value="dots" @checked($settings['qr_style'] === 'dots')><span>Dots</span></label>
                        <label><input type="radio" name="qr_style" value="square" @checked($settings['qr_style'] === 'square')><span>Squares</span></label>
                    </div>
                </div>
                @if (multilang())
                <div>
                    <div class="label">Amharic line</div>
                    <input type="hidden" name="amharic" value="0">
                    <label class="switch"><input type="checkbox" name="amharic" value="1" @checked($settings['amharic'])><span class="track"></span><span lang="am">ምናሌውን ለማየት ይቃኙ</span></label>
                </div>
                @endif
            </div>

            <div class="opt-group">
                <div class="label">QR codes</div>
                <div class="seg">
                    <label><input type="radio" name="mode" value="cafe" @checked($settings['mode'] === 'cafe')><span>One for the café</span></label>
                    <label><input type="radio" name="mode" value="tables" @checked($settings['mode'] === 'tables')><span>One per table</span></label>
                </div>
                <div class="field mt-3" id="tables-field" style="{{ $settings['mode'] === 'tables' ? '' : 'display:none' }}">
                    <label>Tables</label>
                    <input class="input" name="tables" value="{{ $settings['tables'] }}" placeholder="1-12, VIP, Terrace">
                    <div class="hint">Use ranges and names: <code>1-12</code> or <code>1-8, VIP, Garden</code>. Each table gets its own card with its number, so you can see which tables scan most.</div>
                </div>
            </div>
        </div>

        <div class="preview-col">
            <div class="qr-stage"><iframe id="qr-preview" title="Print preview" scrolling="no"></iframe></div>
            <p class="small muted mt-2" style="text-align:center" id="qr-count"></p>
            <div class="stack mt-3">
                <button class="btn btn-brand btn-lg btn-block"><x-icon name="download"/>Download PDF</button>
                <a class="btn btn-block" id="png-link" href="#"><x-icon name="image"/>Download QR image only (PNG)</a>
                <p class="tiny muted" style="text-align:center">Print at 100% scale (“Actual size”). Test-scan one before printing them all.</p>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
(function () {
    const form = document.getElementById('qr-form');
    const frame = document.getElementById('qr-preview');
    const count = document.getElementById('qr-count');
    const png = document.getElementById('png-link');
    const color = document.getElementById('qr-color');
    const previewUrl = @json(route('dashboard.qr.preview'));
    const pngUrl = @json(route('dashboard.qr.png'));
    const papers = @json(config('xmenu.paper'));
    let t;

    function params() {
        const fd = new FormData(form);
        const p = new URLSearchParams();
        for (const [k, v] of fd.entries()) p.set(k, v); // checkbox after hidden wins
        return p;
    }
    function tableCount(spec) {
        let n = 0;
        spec.split(/[,;\n]+/).map(s => s.trim()).filter(Boolean).forEach(s => {
            const m = s.match(/^(\d+)\s*-\s*(\d+)$/);
            n += m ? Math.abs(+m[2] - +m[1]) + 1 : 1;
        });
        return Math.min(Math.max(n, 1), 200);
    }
    function update() {
        const p = params();
        const size = p.get('size');
        const paper = papers[size];
        const W = Math.min(frame.parentElement.clientWidth - 40, 340);
        frame.style.width = W + 'px';
        frame.style.height = Math.round(W * paper.h / paper.w) + 'px';
        form.style.setProperty('--qc', color.value);

        const isTables = p.get('mode') === 'tables';
        document.getElementById('tables-field').style.display = isTables ? '' : 'none';
        document.getElementById('sheet-opt').style.opacity = size === 'A4' ? .4 : 1;
        document.getElementById('tile-label').textContent = size === 'A5' ? '2 on A4' : (size === 'A6' ? '4 on A4' : 'Save paper');
        const wh = document.getElementById('wifi-hint'); if (wh) wh.style.display = p.get('design') === 'wifi' ? '' : 'none';

        const perPage = (size !== 'A4' && p.get('sheet') === 'tile') ? (size === 'A5' ? 2 : 4) : 1;
        const n = isTables ? tableCount(p.get('tables') || '') : perPage;
        count.textContent = n + (n === 1 ? ' card' : ' cards') + ' · ' + Math.ceil(n / perPage) + (Math.ceil(n / perPage) === 1 ? ' page' : ' pages') + ' · ' + size + (perPage > 1 ? ' on A4' : '');

        png.href = pngUrl + '?' + p.toString();
        clearTimeout(t);
        t = setTimeout(() => { frame.src = previewUrl + '?' + p.toString(); }, 180);
    }
    form.addEventListener('change', update);
    form.addEventListener('input', e => { if (e.target.type === 'text' || e.target.type === 'color') update(); });
    form.querySelectorAll('[data-color]').forEach(b => b.addEventListener('click', () => { color.value = b.dataset.color; update(); }));
    addEventListener('resize', update);
    update();
})();
</script>
@endpush
