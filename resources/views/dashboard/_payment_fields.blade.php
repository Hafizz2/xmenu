<div class="field">
    <label>Type</label>
    <select class="input" name="type" data-pay-type>
        @foreach (config('xmenu.payment_types') as $k => $label)
            <option value="{{ $k }}" @selected(($a->type ?? 'bank') === $k)>{{ $label }}</option>
        @endforeach
    </select>
</div>
<div class="field" data-show-for="bank">
    <label>Bank</label>
    <input class="input" name="provider" list="banks" value="{{ $a->provider ?? '' }}" placeholder="Commercial Bank of Ethiopia">
</div>
<div class="field" data-show-for="other">
    <label>Wallet name</label>
    <input class="input" name="provider" value="{{ $a->provider ?? '' }}" placeholder="M-Pesa, Amole…" disabled data-enable-for="other">
</div>
<div class="field">
    <label>Account or phone number</label>
    <input class="input" name="account_number" value="{{ $a->account_number ?? '' }}" required inputmode="numeric" placeholder="1000123456789">
</div>
<div class="field">
    <label>Account name</label>
    <input class="input" name="account_name" value="{{ $a->account_name ?? '' }}" placeholder="Your business name">
</div>
<datalist id="banks">@foreach (config('xmenu.banks') as $b)<option value="{{ $b }}">@endforeach</datalist>
