@extends('layouts.admin')
@section('title', 'Settings')

@php $rows = array_pad($accounts, max(3, count($accounts) + 1), []); @endphp

@section('content')
<div class="page-head"><div><h1>Platform settings</h1><p>How cafés pay you, how to reach support, and trial length.</p></div></div>

<form method="post">
    @csrf @method('put')
    <div class="card card-pad">
        <div class="section-title">Where cafés send subscription payments</div>
        <div class="section-sub">Shown on every owner’s Plan & billing page.</div>
        @foreach ($rows as $i => $a)
            <div class="grid-3 mt-3">
                <div class="field"><label>Bank / wallet</label><input class="input" name="accounts[{{ $i }}][label]" value="{{ $a['label'] ?? '' }}" placeholder="Commercial Bank of Ethiopia"></div>
                <div class="field"><label>Account name</label><input class="input" name="accounts[{{ $i }}][name]" value="{{ $a['name'] ?? '' }}" placeholder="Xmenu PLC"></div>
                <div class="field"><label>Number</label><input class="input" name="accounts[{{ $i }}][number]" value="{{ $a['number'] ?? '' }}"></div>
            </div>
        @endforeach
        <p class="hint">Leave a row’s number empty to remove it.</p>
    </div>

    <div class="card card-pad mt-4">
        <div class="section-title">Support contact</div>
        <div class="grid-3 mt-3">
            <div class="field"><label>Phone</label><input class="input" name="support[phone]" value="{{ $support['phone'] ?? '' }}"></div>
            <div class="field"><label>Email</label><input class="input" name="support[email]" value="{{ $support['email'] ?? '' }}"></div>
            <div class="field"><label>Telegram</label><input class="input" name="support[telegram]" value="{{ $support['telegram'] ?? '' }}" placeholder="@xmenu_support"></div>
        </div>
    </div>

    <div class="card card-pad mt-4">
        <div class="section-title">Free trial</div>
        <div class="field mt-3" style="max-width:220px"><label>Trial length for new cafés</label><div class="input-group"><input class="input" type="number" name="trial_days" min="0" max="365" value="{{ $trialDays }}"><span class="addon">days</span></div></div>
    </div>

    <div class="sticky-save"><button class="btn btn-primary"><x-icon name="check"/>Save settings</button></div>
</form>
@endsection
