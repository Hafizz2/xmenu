@extends('layouts.panel')
@section('title', $item->exists ? 'Edit dish' : 'New dish')
@section('inline-errors', true)

@php
    $o = fn ($k, $default = null) => old($k, $default);
    $variants = old('variants', collect($item->variants ?? [])->map(fn ($v) => ['name_en' => $v['name']['en'] ?? '', 'name_am' => $v['name']['am'] ?? '', 'price' => $v['price']])->all());
    $addons = old('addons', collect($item->addons ?? [])->map(fn ($v) => ['name_en' => $v['name']['en'] ?? '', 'name_am' => $v['name']['am'] ?? '', 'price' => $v['price']])->all());
    $tags = old('tags', $item->tags ?? []);
    $hasAm = $o('name_am', $item->name['am'] ?? '') || $o('description_am', $item->description['am'] ?? '');
@endphp

@section('content')
<div class="page-head">
    <div>
        <a href="{{ route('dashboard.menu') }}" class="crumb"><x-icon name="left" class="sm"/>Menu</a>
        <h1>{{ $item->exists ? tr($item->name) : 'Add a dish' }}</h1>
    </div>
    @if ($item->exists)
        <form method="post" action="{{ route('dashboard.items.duplicate', $item) }}">@csrf<button class="btn btn-sm"><x-icon name="copy" class="sm"/>Make a copy</button></form>
    @endif
</div>

@if ($errors->any())
    <div class="flash err"><x-icon name="x"/>Please fix the fields marked in red.</div>
@endif

<form method="post" enctype="multipart/form-data" action="{{ $item->exists ? route('dashboard.items.update', $item) : route('dashboard.items.store') }}">
    @csrf
    @if ($item->exists) @method('put') @endif

    <div class="dish-form">
        {{-- 1. Photo: the first thing customers look at --}}
        <div class="card card-pad">
            <label class="photo-drop dish-photo" data-photo>
                @if ($item->image)<img src="{{ img_url($item->image, 640) }}" alt="">@endif
                <span class="pd-ic"><x-icon name="image" class="lg"/></span>
                <b>{{ $item->image ? 'Tap to change the photo' : 'Tap to add a photo' }}</b>
                <span class="small">Take one with your phone or pick one from your gallery.</span>
                <input type="file" name="photo" accept="image/*">
            </label>
            @error('photo')<div class="error">{{ $message }}</div>@enderror
            @if ($item->image)
                <label class="row small mt-3 muted"><input type="checkbox" name="remove_photo" value="1"> Remove this photo</label>
            @endif
        </div>

        {{-- 2. The basics --}}
        <div class="card card-pad stack-lg">
            <div class="field">
                <label for="f-name">Dish name</label>
                <input id="f-name" class="input input-lg @error('name_en') is-invalid @enderror" name="name_en" value="{{ $o('name_en', $item->name['en'] ?? '') }}" maxlength="100" placeholder="e.g. Avocado juice" @unless ($item->exists) autofocus @endunless>
                @error('name_en')<div class="error">{{ $message }}</div>@enderror
            </div>
            <div class="grid-2">
                <div class="field">
                    <label for="f-price">Price</label>
                    <div class="input-group">
                        <input id="f-price" class="input input-lg @error('price') is-invalid @enderror" name="price" type="number" step="0.01" min="0" inputmode="decimal" value="{{ $o('price', $item->exists ? $item->price + 0 : '') }}" placeholder="0" required style="font-weight:700">
                        <span class="addon">{{ $cafe->currency }}</span>
                    </div>
                    @error('price')<div class="error">{{ $message }}</div>@enderror
                </div>
                <div class="field">
                    <label for="f-cat">Category</label>
                    <select id="f-cat" class="input input-lg" name="category_id">
                        @foreach ($categories as $c)
                            <option value="{{ $c->id }}" @selected($o('category_id', $item->category_id) == $c->id)>{{ tr($c->name) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="field">
                <label for="f-desc">Short description <span class="opt">optional</span></label>
                <textarea id="f-desc" class="input" name="description_en" maxlength="500" rows="2" placeholder="e.g. Fresh avocado with a squeeze of lime">{{ $o('description_en', $item->description['en'] ?? '') }}</textarea>
            </div>
        </div>

        {{-- 3. Amharic (only when Amharic is switched on) --}}
        @if (multilang())
        <details class="card fold" @if ($hasAm) open @endif>
            <summary><span><b>In Amharic <span class="opt">optional</span></b><small>Customers who switch the menu to አማርኛ see this.</small></span><x-icon name="down" class="chev"/></summary>
            <div class="fold-body stack-lg">
                <div class="field">
                    <label for="f-name-am">Dish name in Amharic</label>
                    <input id="f-name-am" class="input input-lg" name="name_am" value="{{ $o('name_am', $item->name['am'] ?? '') }}" maxlength="100" placeholder="የአቮካዶ ጭማቂ" lang="am">
                </div>
                <div class="field">
                    <label for="f-desc-am">Description in Amharic</label>
                    <textarea id="f-desc-am" class="input" name="description_am" maxlength="500" rows="2" lang="am">{{ $o('description_am', $item->description['am'] ?? '') }}</textarea>
                </div>
            </div>
        </details>
        @endif

        {{-- 4. Labels --}}
        <div class="card card-pad">
            <div class="section-title">Labels <span class="opt">optional</span></div>
            <div class="section-sub">Tap any that fit. Customers can filter by them, for example to see only fasting food.</div>
            <div class="pills mt-3">
                @foreach (config('xmenu.tags') as $key => $tag)
                    <label class="pill"><input type="checkbox" name="tags[]" value="{{ $key }}" @checked(in_array($key, $tags))><span><x-icon :name="$tag['icon']"/>{{ $tag['en'] }}</span></label>
                @endforeach
            </div>
        </div>

        {{-- 5. Sizes and extras, folded away until needed --}}
        <details class="card fold" @if (count($variants) || count($addons)) open @endif>
            <summary><span><b>Sizes and extras <span class="opt">optional</span></b><small>Small and Large at different prices, or extras like cheese.</small></span><x-icon name="down" class="chev"/></summary>
            <div class="fold-body">
                @foreach (['variants' => ['Sizes', 'Each size has its own price.', 'Small', 'ትንሽ', $variants, 'Add a size'], 'addons' => ['Extras', 'Things customers can add, with their price.', 'Extra cheese', 'ተጨማሪ አይብ', $addons, 'Add an extra']] as $field => [$title, $sub, $phEn, $phAm, $rows, $add])
                    <div data-repeater class="{{ $loop->first ? '' : 'mt-6' }}">
                        <div class="label" style="margin-bottom:2px">{{ $title }}</div>
                        <div class="hint" style="margin:0 0 10px">{{ $sub }}</div>
                        <div data-rows>
                            @foreach ($rows as $i => $r)
                                <div class="repeater-row {{ multilang() ? '' : 'one-lang' }}">
                                    <input class="input" name="{{ $field }}[{{ $i }}][name_en]" value="{{ $r['name_en'] ?? '' }}" placeholder="{{ $phEn }}" aria-label="Name">
                                    @if (multilang())<input class="input am" name="{{ $field }}[{{ $i }}][name_am]" value="{{ $r['name_am'] ?? '' }}" placeholder="{{ $phAm }}" lang="am" aria-label="Name in Amharic">@else<input type="hidden" name="{{ $field }}[{{ $i }}][name_am]" value="{{ $r['name_am'] ?? '' }}">@endif
                                    <input class="input" name="{{ $field }}[{{ $i }}][price]" value="{{ $r['price'] ?? '' }}" type="number" step="0.01" min="0" inputmode="decimal" placeholder="Price" aria-label="Price">
                                    <button type="button" class="btn btn-ghost btn-icon" data-remove aria-label="Remove"><x-icon name="trash" class="sm"/></button>
                                </div>
                            @endforeach
                        </div>
                        <template>
                            <div class="repeater-row {{ multilang() ? '' : 'one-lang' }}">
                                <input class="input" name="{{ $field }}[__i__][name_en]" placeholder="{{ $phEn }}" aria-label="Name">
                                @if (multilang())<input class="input am" name="{{ $field }}[__i__][name_am]" placeholder="{{ $phAm }}" lang="am" aria-label="Name in Amharic">@endif
                                <input class="input" name="{{ $field }}[__i__][price]" type="number" step="0.01" min="0" inputmode="decimal" placeholder="Price" aria-label="Price">
                                <button type="button" class="btn btn-ghost btn-icon" data-remove aria-label="Remove"><x-icon name="trash" class="sm"/></button>
                            </div>
                        </template>
                        <button type="button" class="btn btn-sm" data-add><x-icon name="plus" class="sm"/>{{ $add }}</button>
                    </div>
                @endforeach
            </div>
        </details>

        {{-- 6. Status --}}
        <div class="card card-pad stack-lg">
            <label class="switch"><input type="checkbox" name="visible" value="1" @checked($o('visible', $item->visible ?? true))><span class="track"></span><span><b>Show on the menu</b><br><span class="small muted">Turn off to hide it without deleting.</span></span></label>
            <label class="switch danger"><input type="checkbox" name="sold_out" value="1" @checked($o('sold_out', $item->sold_out))><span class="track"></span><span><b>Sold out today</b><br><span class="small muted">Customers still see it, marked as sold out.</span></span></label>
        </div>

        @unless ($item->exists)<button class="btn btn-lg btn-block after-form" name="after" value="another"><x-icon name="plus" class="sm"/>Save and add another dish</button>@endunless
        @if ($item->exists)
            <button type="submit" form="del-item" class="btn btn-ghost btn-danger btn-block"><x-icon name="trash" class="sm"/>Delete this dish</button>
        @endif
    </div>

    <div class="save-bar">
        <button class="btn btn-brand btn-lg" name="after" value="menu"><x-icon name="check"/>Save dish</button>
        @unless ($item->exists)<button class="btn btn-lg" name="after" value="another">Save and add another</button>@endunless
    </div>
</form>

@if ($item->exists)
    <form id="del-item" method="post" action="{{ route('dashboard.items.destroy', $item) }}" data-confirm="Delete this dish? This cannot be undone.">@csrf @method('delete')</form>
@endif
@endsection
