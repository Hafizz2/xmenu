@extends('layouts.panel')
@section('title', 'Café info')

@section('content')
<div class="page-head">
    <div>
        <h1>Café info</h1>
        <p>What customers see on the Info tab, and on your QR prints.</p>
    </div>
</div>

<form method="post" enctype="multipart/form-data">
    @csrf @method('put')

    <div class="card card-pad">
        <div class="section-title">Brand</div>
        <div class="grid-2 mt-4">
            <div>
                <div class="label">Logo</div>
                <div class="brand-up">
                    <label class="logo-prev" id="logo-prev" style="cursor:pointer">
                        @if ($cafe->logo)<img src="{{ $cafe->logoUrl(256) }}" alt="">@else<x-icon name="image"/>@endif
                    </label>
                    <div>
                        <label class="btn btn-sm" data-photo="#logo-prev" style="position:relative;overflow:hidden">
                            <x-icon name="upload" class="sm"/>{{ $cafe->logo ? 'Change' : 'Upload' }} logo
                            <input type="file" name="logo" accept="image/*" style="position:absolute;inset:0;opacity:0;cursor:pointer">
                        </label>
                        <p class="tiny muted mt-2">Square works best. PNG with a transparent background is ideal.</p>
                        @if ($cafe->logo)<label class="row tiny muted mt-1"><input type="checkbox" name="remove_logo" value="1"> Remove</label>@endif
                    </div>
                </div>
            </div>
            <div>
                <div class="label">Cover photo</div>
                <label class="cover-prev" id="cover-prev" data-photo="#cover-prev" style="position:relative;overflow:hidden;cursor:pointer;{{ $cafe->cover ? 'background-image:url('.$cafe->coverUrl(640).')' : '' }}">
                    @unless ($cafe->cover)<span class="small row" style="gap:6px"><x-icon name="image" class="sm"/>Add a photo of your café</span>@endunless
                    <input type="file" name="cover" accept="image/*" style="position:absolute;inset:0;opacity:0;cursor:pointer">
                </label>
                @if ($cafe->cover)<label class="row tiny muted mt-1"><input type="checkbox" name="remove_cover" value="1"> Remove</label>@endif
            </div>
        </div>
        @error('logo')<div class="error">{{ $message }}</div>@enderror
        <hr class="divider">
        <div class="field">
            <label>Café name</label>
            <input class="input" name="name" value="{{ old('name', $cafe->name) }}" required maxlength="80">
        </div>
        <div class="{{ multilang() ? 'grid-2' : '' }} mt-3">
            <div class="field"><label>Tagline</label><input class="input" name="tagline_en" value="{{ old('tagline_en', $cafe->tagline['en'] ?? '') }}" maxlength="120" placeholder="Fresh coffee since 2015"></div>
            @if (multilang())<div class="field"><label>Tagline <span class="lang-tag">አማ</span></label><input class="input" name="tagline_am" value="{{ old('tagline_am', $cafe->tagline['am'] ?? '') }}" maxlength="120" lang="am"></div>@endif
        </div>
    </div>

    <div class="card card-pad mt-4">
        <div class="section-title">Contact & location</div>
        <div class="grid-2 mt-4">
            <div class="field"><label>Phone</label><input class="input" name="phone" type="tel" value="{{ old('phone', $cafe->phone) }}" placeholder="+251 9…"></div>
            <div class="field"><label>Address</label><input class="input" name="address" value="{{ old('address', $cafe->address) }}" placeholder="Bole Road, near Edna Mall"></div>
        </div>
        <div class="field mt-3">
            <label>Google Maps link</label>
            <input class="input @error('map_url') is-invalid @enderror" name="map_url" value="{{ old('map_url', $cafe->map_url) }}" placeholder="https://maps.app.goo.gl/…">
            <div class="hint">In Google Maps, find your café, tap <b>Share</b> and paste the link here.</div>
            @error('map_url')<div class="error">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="card card-pad mt-4">
        <div class="section-title row" style="gap:8px"><x-icon name="wifi"/>Wi-Fi</div>
        <div class="section-sub">Customers copy it with one tap. No more asking the waiter.</div>
        <div class="grid-2 mt-4">
            <div class="field"><label>Network name</label><input class="input" name="wifi_name" value="{{ old('wifi_name', $cafe->wifi_name) }}" autocomplete="off"></div>
            <div class="field"><label>Password</label><input class="input" name="wifi_password" value="{{ old('wifi_password', $cafe->wifi_password) }}" autocomplete="off"></div>
        </div>
    </div>

    <div class="card card-pad mt-4">
        <div class="section-title row" style="gap:8px"><x-icon name="clock"/>Opening hours</div>
        <div class="section-sub">Your menu shows “Open now” or “Closed now” automatically.</div>
        <div class="mt-3">
            @foreach (['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'] as $d => $day)
                @php $h = $hours[$d] ?? null; @endphp
                <div class="hours-row">
                    <b class="small"><span class="full">{{ $day }}</span><span class="short">{{ substr($day, 0, 3) }}</span></b>
                    <input class="input" type="time" name="hours[{{ $d }}][opens_at]" value="{{ $h?->opens_at ? substr($h->opens_at, 0, 5) : '07:00' }}">
                    <input class="input" type="time" name="hours[{{ $d }}][closes_at]" value="{{ $h?->closes_at ? substr($h->closes_at, 0, 5) : '22:00' }}">
                    <label class="row tiny"><input type="checkbox" name="hours[{{ $d }}][closed]" value="1" @checked($h?->closed)> Closed</label>
                </div>
            @endforeach
        </div>
    </div>

    <div class="card card-pad mt-4">
        <div class="section-title">Social links</div>
        <div class="section-sub">A username or a full link both work.</div>
        <div class="grid-2 mt-4">
            @foreach (config('xmenu.socials') as $key => $label)
                <div class="field"><label>{{ $label }}</label><input class="input" name="socials[{{ $key }}]" value="{{ old("socials.$key", $cafe->socials[$key] ?? '') }}" placeholder="{{ $key === 'website' ? 'www.yourcafe.com' : ($key === 'whatsapp' ? '+2519…' : '@yourcafe') }}"></div>
            @endforeach
        </div>
    </div>

    <div class="card card-pad mt-4">
        <div class="section-title">Prices</div>
        <div class="grid-2 mt-4" style="align-items:center">
            <label class="switch"><input type="checkbox" name="vat_included" value="1" @checked(old('vat_included', $cafe->vat_included))><span class="track"></span>Prices include VAT</label>
            <div class="field">
                <label>Service charge</label>
                <div class="input-group"><input class="input" name="service_charge_pct" type="number" step="0.5" min="0" max="50" value="{{ old('service_charge_pct', $cafe->service_charge_pct + 0) }}"><span class="addon">%</span></div>
            </div>
        </div>
        <p class="hint">A small note at the bottom of your menu tells customers this.</p>
    </div>

    <div class="sticky-save"><button class="btn btn-primary"><x-icon name="check"/>Save details</button></div>
</form>
@endsection
