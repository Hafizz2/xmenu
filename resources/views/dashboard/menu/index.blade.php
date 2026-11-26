@extends('layouts.panel')
@section('title', 'Menu')

@section('content')
<div class="page-head">
    <div>
        <h1>Menu</h1>
        <p>Type a new price or tap <b>Available</b> to mark a dish sold out. Changes save by themselves. Tap a dish to edit everything else.</p>
    </div>
    <div class="row row-wrap">
        @if ($categories->isNotEmpty())
            <a href="{{ route('dashboard.items.create') }}" class="btn btn-primary"><x-icon name="plus" class="sm"/>Add a dish</a>
        @endif
        <button type="button" class="btn" data-dialog="dlg-cat"><x-icon name="plus" class="sm"/>Add a category</button>
        <a href="{{ route('dashboard.import') }}" class="btn btn-ghost"><x-icon name="upload" class="sm"/>Import from Excel</a>
    </div>
</div>

@if ($categories->isEmpty())
    <div class="card empty">
        <div class="glyph"><x-icon name="book"/></div>
        <h3>Start with a category</h3>
        <p>Group your menu the way customers think: Coffee, Breakfast, Juice, Burgers.</p>
        <div class="row mt-6" style="justify-content:center;flex-wrap:wrap">
            <button type="button" class="btn btn-primary" data-dialog="dlg-cat"><x-icon name="plus" class="sm"/>Add first category</button>
            <a href="{{ route('dashboard.import') }}" class="btn"><x-icon name="upload" class="sm"/>Import a spreadsheet</a>
        </div>
    </div>
@else
    <nav class="tabs-line" aria-label="Jump to category" style="margin-bottom:18px">
        @foreach ($categories as $cat)
            <a href="#cat-{{ $cat->id }}">{{ tr($cat->name) }} <span>{{ $cat->items->count() }}</span></a>
        @endforeach
    </nav>
@endif

@foreach ($categories as $ci => $cat)
    <section class="card cat" id="cat-{{ $cat->id }}" style="scroll-margin-top:80px">
        <div class="cat-head">
            <div class="reorder">
                <form method="post" action="{{ route('dashboard.categories.move', $cat) }}">@csrf<input type="hidden" name="dir" value="up"><button title="Move up" aria-label="Move category up" {{ $ci === 0 ? 'disabled' : '' }}><x-icon name="up" class="sm"/></button></form>
                <form method="post" action="{{ route('dashboard.categories.move', $cat) }}">@csrf<input type="hidden" name="dir" value="down"><button title="Move down" aria-label="Move category down" {{ $loop->last ? 'disabled' : '' }}><x-icon name="down" class="sm"/></button></form>
            </div>
            <span class="cat-icon"><x-icon :name="$cat->icon ?: 'utensils'"/></span>
            <h3 class="grow"><span style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ tr($cat->name) }}</span>
                @if (multilang() && ! empty($cat->name['am']) && ! empty($cat->name['en']))<span class="small muted" style="font-weight:400">{{ $cat->name['am'] }}</span>@endif
                @unless ($cat->visible)<span class="badge">Hidden</span>@endunless
            </h3>
            <a href="{{ route('dashboard.items.create', ['category' => $cat->id]) }}" class="btn btn-sm" aria-label="Add a dish to {{ tr($cat->name) }}"><x-icon name="plus" class="sm"/>Add</a>
            <div class="dropdown">
                <button class="btn btn-sm btn-ghost btn-icon" data-dropdown aria-label="Category options"><x-icon name="dots"/></button>
                <div class="dropdown-menu">
                    <button type="button" data-dialog="dlg-cat-{{ $cat->id }}"><x-icon name="edit" class="sm"/>Edit category</button>
                    <hr>
                    <form method="post" action="{{ route('dashboard.categories.destroy', $cat) }}" data-confirm="Delete “{{ tr($cat->name) }}” and its {{ $cat->items->count() }} items? This can’t be undone.">
                        @csrf @method('delete')
                        <button class="danger"><x-icon name="trash" class="sm"/>Delete category</button>
                    </form>
                </div>
            </div>
        </div>

        @if ($cat->items->isEmpty())
            <div class="empty" style="padding:26px"><p class="small">No items yet. <a href="{{ route('dashboard.items.create', ['category' => $cat->id]) }}">Add the first one</a></p></div>
        @else
            <ul class="items">
                @foreach ($cat->items as $ii => $item)
                    <li class="it {{ $item->visible ? '' : 'is-off' }} {{ $item->sold_out ? 'is-sold' : '' }}">
                        <div class="reorder">
                            <form method="post" action="{{ route('dashboard.items.move', $item) }}">@csrf<input type="hidden" name="dir" value="up"><button title="Move up" aria-label="Move up" {{ $ii === 0 ? 'disabled' : '' }}><x-icon name="up" class="sm"/></button></form>
                            <form method="post" action="{{ route('dashboard.items.move', $item) }}">@csrf<input type="hidden" name="dir" value="down"><button title="Move down" aria-label="Move down" {{ $loop->last ? 'disabled' : '' }}><x-icon name="down" class="sm"/></button></form>
                        </div>
                        <a href="{{ route('dashboard.items.edit', $item) }}" class="thumb {{ $item->image ? '' : 'empty' }}" aria-label="Edit {{ tr($item->name) }}">
                            @if ($item->image)<img src="{{ img_url($item->image, 320) }}" alt="" loading="lazy">@else<x-icon name="image"/><small>Add photo</small>@endif
                        </a>
                        <div class="it-main">
                            <a href="{{ route('dashboard.items.edit', $item) }}" class="it-name">
                                <span class="nm">{{ tr($item->name) }}</span>
                                <span class="sub">
                                    @unless ($item->visible)<span class="badge">Hidden</span>@endunless
                                    @if ($item->hasVariants())<span>{{ count($item->variants) }} sizes</span>@endif
                                    <span class="desc">{{ tr($item->description) }}</span>
                                </span>
                            </a>
                            <div class="it-ctrl">
                                <label class="price-box" title="{{ $item->hasVariants() ? 'Base price. Open the item to change size prices.' : 'Type a new price. It saves by itself.' }}">
                                    <input class="price-in" type="number" inputmode="decimal" step="0.01" min="0" value="{{ $item->price + 0 }}" data-quick-price="{{ route('dashboard.items.quick', $item) }}" aria-label="Price of {{ tr($item->name) }}">
                                    <span>{{ $cafe->currency }}</span>
                                </label>
                                <label class="avail">
                                    <input type="checkbox" {{ $item->sold_out ? 'checked' : '' }} data-quick-toggle="{{ route('dashboard.items.quick', $item) }}" data-field="sold_out" aria-label="Sold out">
                                    <span class="yes"><i></i>Available</span><span class="no"><i></i>Sold out</span>
                                </label>
                            </div>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-ghost btn-icon" data-dropdown aria-label="Item options"><x-icon name="dots"/></button>
                            <div class="dropdown-menu">
                                <a href="{{ route('dashboard.items.edit', $item) }}"><x-icon name="edit" class="sm"/>Edit</a>
                                <label><input type="checkbox" {{ $item->visible ? 'checked' : '' }} data-quick-toggle="{{ route('dashboard.items.quick', $item) }}" data-field="visible"> Show on menu</label>
                                <form method="post" action="{{ route('dashboard.items.duplicate', $item) }}">@csrf<button><x-icon name="copy" class="sm"/>Duplicate</button></form>
                                @if ($ii > 0)<form method="post" action="{{ route('dashboard.items.move', $item) }}" class="only-mobile">@csrf<input type="hidden" name="dir" value="up"><button><x-icon name="up" class="sm"/>Move up</button></form>@endif
                                @unless ($loop->last)<form method="post" action="{{ route('dashboard.items.move', $item) }}" class="only-mobile">@csrf<input type="hidden" name="dir" value="down"><button><x-icon name="down" class="sm"/>Move down</button></form>@endunless
                                <hr>
                                <form method="post" action="{{ route('dashboard.items.destroy', $item) }}" data-confirm="Delete “{{ tr($item->name) }}”?">@csrf @method('delete')<button class="danger"><x-icon name="trash" class="sm"/>Delete</button></form>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </section>

    <dialog id="dlg-cat-{{ $cat->id }}">
        <form method="post" action="{{ route('dashboard.categories.update', $cat) }}">
            @csrf @method('put')
            <div class="dlg-head"><h3>Edit category</h3><button class="btn btn-ghost btn-icon btn-sm" data-close aria-label="Close"><x-icon name="x"/></button></div>
            <div class="dlg-body stack">
                @include('dashboard.menu._category_fields', ['c' => $cat])
                <label class="switch"><input type="checkbox" name="visible" value="1" {{ $cat->visible ? 'checked' : '' }}><span class="track"></span>Show on menu</label>
            </div>
            <div class="dlg-foot"><button type="button" class="btn" data-close>Cancel</button><button class="btn btn-primary">Save</button></div>
        </form>
    </dialog>
@endforeach

<dialog id="dlg-cat">
    <form method="post" action="{{ route('dashboard.categories.store') }}" id="new-cat">
        @csrf
        <div class="dlg-head"><h3>New category</h3><button class="btn btn-ghost btn-icon btn-sm" data-close aria-label="Close"><x-icon name="x"/></button></div>
        <div class="dlg-body stack">
            <div>
                <div class="label">Quick start</div>
                <div class="pills">
                    @foreach ([['coffee', 'Coffee', 'ቡና'], ['breakfast', 'Breakfast', 'ቁርስ'], ['juice', 'Juice', 'ጭማቂ'], ['burger', 'Burgers', 'በርገር'], ['pizza', 'Pizza', 'ፒዛ'], ['cake', 'Cakes', 'ኬኮች'], ['traditional', 'Traditional', 'ባህላዊ'], ['salad', 'Salads', 'ሰላጣ'], ['tea', 'Tea', 'ሻይ']] as [$ic, $en, $am])
                        <button type="button" class="btn btn-xs" data-quick-cat='@json([$ic, $en, $am])'><x-icon :name="$ic" class="sm"/>{{ $en }}</button>
                    @endforeach
                </div>
            </div>
            @include('dashboard.menu._category_fields', ['c' => null])
        </div>
        <div class="dlg-foot"><button type="button" class="btn" data-close>Cancel</button><button class="btn btn-primary">Add category</button></div>
    </form>
</dialog>
@endsection

@push('scripts')
<script>
document.querySelectorAll('[data-quick-cat]').forEach(b => b.addEventListener('click', () => {
    const [ic, en, am] = JSON.parse(b.dataset.quickCat), f = document.getElementById('new-cat');
    f.name_en.value = en; if (f.name_am) f.name_am.value = am;
    const r = f.querySelector('input[name=icon][value="' + ic + '"]'); if (r) r.checked = true;
}));
</script>
@endpush
