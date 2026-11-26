@php $uid = $c->id ?? 'new'; $current = $c->icon ?? 'utensils'; @endphp
<div class="{{ multilang() ? 'grid-2' : '' }}">
    <div class="field">
        <label>Category name</label>
        <input class="input" name="name_en" value="{{ $c->name['en'] ?? '' }}" maxlength="60" placeholder="Coffee" {{ $c ? '' : 'autofocus' }}>
    </div>
    @if (multilang())
    <div class="field">
        <label>Name <span class="lang-tag">አማ</span></label>
        <input class="input" name="name_am" value="{{ $c->name['am'] ?? '' }}" maxlength="60" placeholder="ቡና" lang="am">
    </div>
    @endif
</div>
<div class="field">
    <label>Icon</label>
    <div class="icon-pick">
        @foreach (\App\Support\Icons::CATEGORY as $key => $label)
            <label title="{{ $label }}"><input type="radio" name="icon" value="{{ $key }}" @checked($current === $key)><span><x-icon :name="$key"/></span></label>
        @endforeach
    </div>
</div>
