@extends('layouts.panel')
@section('title', 'Plan & billing')

@php $pending = $subs->firstWhere('status', 'pending'); @endphp

@section('content')
<div class="page-head"><div><h1>Plan & billing</h1><p>Pay by bank transfer or Telebirr, upload your receipt, and we activate your plan.</p></div></div>

<div class="card card-pad">
    <div class="row between row-wrap">
        <div>
            <div class="section-title">
                @if ($cafe->status === 'trial') Free trial
                @else {{ ucfirst($cafe->plan) }} plan @endif
                <span class="badge {{ $cafe->statusTone() }}">{{ $cafe->status }}</span>
            </div>
            <p class="muted small mt-1">
                @if ($cafe->status === 'trial' && $cafe->trial_ends_at) Trial ends {{ $cafe->trial_ends_at->format('j M Y') }} ({{ $cafe->trialDaysLeft() }} days left).
                @elseif ($cafe->paid_until) Paid until {{ $cafe->paid_until->format('j M Y') }}{{ $cafe->paid_until->isPast() ? ' (expired)' : '' }}.
                @else Choose a plan below to keep your menu online. @endif
            </p>
        </div>
        @if ($pending)<span class="badge warn">Payment under review</span>@endif
    </div>
</div>

@if ($plans->isNotEmpty())
<div class="grid-3 mt-4" style="align-items:stretch">
    @foreach ($plans as $p)
        <div class="card card-pad" style="{{ $cafe->plan === $p->key && $cafe->status === 'active' ? 'border-color:var(--ink);box-shadow:0 0 0 3px rgba(22,22,26,.08)' : '' }}">
            <div class="section-title">{{ $p->name }}</div>
            @if ($p->tagline)<p class="small muted mt-1">{{ $p->tagline }}</p>@endif
            <div class="mt-3"><span style="font-size:28px;font-weight:800">{{ number_format($p->price) }}</span> <span class="muted">{{ $p->currency }}/month</span></div>
            <ul class="small mt-3" style="padding-left:18px;margin-bottom:0">@foreach ($p->features ?? [] as $f)<li style="margin-top:4px">{{ $f }}</li>@endforeach</ul>
        </div>
    @endforeach
</div>
@endif

@unless ($pending)
<form method="post" enctype="multipart/form-data" class="card card-pad mt-4">
    @csrf
    <div class="section-title">Pay for your plan</div>
    @if ($accounts)
        <p class="small muted mt-1">1. Send the payment to one of these accounts:</p>
        <ul class="list mt-2" style="border:1px solid var(--line);border-radius:var(--r)">
            @foreach ($accounts as $a)
                <li style="padding:12px 16px"><div class="grow"><b class="small">{{ $a['label'] ?? '' }}</b><br><span class="mono">{{ $a['number'] }}</span> <span class="tiny muted">{{ $a['name'] ?? '' }}</span></div>
                    <button type="button" class="btn btn-xs" data-copy="{{ $a['number'] }}">Copy</button></li>
            @endforeach
        </ul>
        <p class="small muted mt-3">2. Fill in the details and upload a screenshot or photo of the receipt:</p>
    @else
        <p class="small muted mt-1">Contact Xmenu support for payment details, then upload your receipt here.</p>
    @endif
    <div class="grid-2 mt-3">
        <div class="field"><label>Plan</label>
            <select class="input" name="plan" id="b-plan">@foreach ($plans as $p)<option value="{{ $p->key }}" data-price="{{ $p->price }}" @selected(old('plan', $cafe->plan) === $p->key)>{{ $p->name }}</option>@endforeach</select></div>
        <div class="field"><label>Pay for</label>
            <select class="input" name="months" id="b-months">@foreach ([1 => '1 month', 3 => '3 months', 6 => '6 months', 12 => '12 months'] as $m => $l)<option value="{{ $m }}" @selected(old('months') == $m)>{{ $l }}</option>@endforeach</select></div>
        <div class="field"><label>Paid with</label>
            <select class="input" name="method">@foreach (['bank' => 'Bank transfer', 'telebirr' => 'Telebirr', 'cbe_birr' => 'CBE Birr', 'other' => 'Other'] as $k => $l)<option value="{{ $k }}">{{ $l }}</option>@endforeach</select></div>
        <div class="field"><label>Transaction / reference number</label><input class="input" name="reference" value="{{ old('reference') }}" required></div>
    </div>
    <div class="field mt-3"><label>Receipt</label><input class="input" type="file" name="receipt" accept="image/*,application/pdf" required style="padding-top:10px"><div class="hint">Photo, screenshot or PDF, up to 5 MB.</div></div>
    <div class="row between mt-4 row-wrap">
        <div>Total: <b id="b-total" style="font-size:20px"></b> <span class="muted">{{ $plans->first()->currency ?? 'ETB' }}</span></div>
        <button class="btn btn-primary"><x-icon name="upload"/>Submit payment</button>
    </div>
</form>
@endunless

@if ($subs->isNotEmpty())
<div class="card mt-4">
    <div class="card-head"><h2>Payment history</h2></div>
    <ul class="list">
        @foreach ($subs as $s)
            <li style="padding:12px 20px">
                <div class="grow"><b class="small">{{ number_format($s->amount) }} {{ $s->currency }}</b> <span class="tiny muted">· {{ ucfirst($s->plan) }} × {{ $s->months }} mo · {{ $s->created_at->format('j M Y') }}</span>
                    @if ($s->status === 'rejected' && $s->note)<br><span class="tiny" style="color:var(--bad)">{{ $s->note }}</span>@endif
                    @if ($s->period_end)<br><span class="tiny muted">Covers until {{ $s->period_end->format('j M Y') }}</span>@endif</div>
                <span class="badge {{ ['approved' => 'ok', 'pending' => 'warn', 'rejected' => 'bad'][$s->status] }}">{{ $s->status === 'pending' ? 'under review' : $s->status }}</span>
            </li>
        @endforeach
    </ul>
</div>
@endif

@if ($support)
    <p class="small muted mt-4">Questions? @if (! empty($support['phone']))Call {{ $support['phone'] }}@endif @if (! empty($support['telegram'])) · Telegram {{ $support['telegram'] }}@endif @if (! empty($support['email'])) · {{ $support['email'] }}@endif</p>
@endif
@endsection

@push('scripts')
<script>
(function () {
    const p = document.getElementById('b-plan'), m = document.getElementById('b-months'), t = document.getElementById('b-total');
    if (!p) return;
    const calc = () => { t.textContent = (parseFloat(p.selectedOptions[0]?.dataset.price || 0) * parseInt(m.value)).toLocaleString(); };
    p.addEventListener('change', calc); m.addEventListener('change', calc); calc();
})();
</script>
@endpush
