@extends('layouts.admin')
@section('title', 'Sales')

@php $cur = $plans->first()->currency ?? 'ETB'; @endphp

@section('content')
<div class="page-head">
    <div><h1>Sales</h1><p>Subscription payments from cafés. Approve uploaded receipts or record cash and transfers.</p></div>
    <a href="#record" class="btn btn-primary" data-dialog="dlg-record"><x-icon name="plus"/>Record payment</a>
</div>

<div class="kpis">
    <div class="kpi"><div class="l">MRR</div><div class="n">{{ number_format($summary['mrr']) }}</div><div class="d">{{ $cur }} per month</div></div>
    <div class="kpi"><div class="l">This month</div><div class="n">{{ number_format($summary['month']) }}</div><div class="d">{{ number_format($summary['year']) }} this year</div></div>
    <div class="kpi"><div class="l">Paying cafés</div><div class="n">{{ $summary['paying'] }}</div><div class="d">ARPU {{ number_format($summary['arpu']) }} {{ $cur }}</div></div>
    <div class="kpi"><div class="l">All-time revenue</div><div class="n">{{ number_format($summary['total']) }}</div><div class="d">{{ $cur }}</div></div>
</div>

<div class="grid-2 mt-4" style="align-items:start">
    <div class="card"><div class="card-head"><h2>By plan</h2></div>
        <table class="tbl">@forelse ($byPlan as $r)<tr><td>{{ ucfirst($r->plan) }}</td><td class="muted">{{ $r->n }} payments</td><td style="text-align:right"><b>{{ number_format($r->total) }}</b></td></tr>@empty<tr><td class="muted">No sales yet.</td></tr>@endforelse</table>
    </div>
    <div class="card"><div class="card-head"><h2>By method</h2></div>
        <table class="tbl">@forelse ($byMethod as $r)<tr><td>{{ \App\Models\Subscription::METHODS[$r->method] ?? $r->method }}</td><td class="muted">{{ $r->n }} payments</td><td style="text-align:right"><b>{{ number_format($r->total) }}</b></td></tr>@empty<tr><td class="muted">No sales yet.</td></tr>@endforelse</table>
    </div>
</div>

<div class="tabs-line mt-6">
    @foreach (['pending' => 'Waiting for approval', 'approved' => 'Approved', 'rejected' => 'Rejected', 'all' => 'All'] as $k => $l)
        <a href="{{ route('admin.sales.index', ['status' => $k]) }}" class="{{ $status === $k ? 'on' : '' }}">{{ $l }} {{ $k === 'all' ? $counts->sum() : ($counts[$k] ?? 0) }}</a>
    @endforeach
</div>

<div class="card scroll-x">
    <table class="tbl cards">
        <thead><tr><th>Café</th><th>Plan</th><th style="text-align:right">Amount</th><th>Method / ref</th><th>Receipt</th><th>Date</th><th>Status</th><th></th></tr></thead>
        <tbody>
        @forelse ($subs as $s)
            <tr>
                <td>@if ($s->cafe)<a href="{{ route('admin.cafes.show', $s->cafe) }}"><b>{{ $s->cafe->name }}</b></a><br><span class="tiny muted">{{ $s->cafe->owner?->email }}</span>@else<span class="muted">deleted</span>@endif</td>
                <td class="small">{{ ucfirst($s->plan) }} × {{ $s->months }} mo</td>
                <td style="text-align:right"><b>{{ number_format($s->amount) }}</b> <span class="tiny muted">{{ $s->currency }}</span></td>
                <td class="small">{{ \App\Models\Subscription::METHODS[$s->method] ?? $s->method }}<br><span class="tiny muted mono">{{ $s->reference ?: '—' }}</span></td>
                <td>@if ($s->receipt)<a href="{{ route('admin.sales.receipt', $s) }}" target="_blank" class="btn btn-xs"><x-icon name="file" class="sm"/>View</a>@else<span class="muted small">—</span>@endif</td>
                <td class="small muted">{{ $s->created_at->format('j M Y H:i') }}@if ($s->approver)<br>by {{ $s->approver->name }}@endif</td>
                <td><span class="badge {{ ['approved' => 'ok', 'pending' => 'warn', 'rejected' => 'bad'][$s->status] }}">{{ $s->status }}</span>
                    @if ($s->period_end)<br><span class="tiny muted">until {{ $s->period_end->format('j M Y') }}</span>@endif
                    @if ($s->status === 'rejected' && $s->note)<br><span class="tiny muted">{{ $s->note }}</span>@endif</td>
                <td style="white-space:nowrap">
                    @if ($s->status === 'pending')
                        <form method="post" action="{{ route('admin.sales.approve', $s) }}" style="display:inline" data-confirm="Approve {{ number_format($s->amount) }} {{ $s->currency }} from {{ $s->cafe?->name }}? Their plan is extended by {{ $s->months }} month(s).">@csrf<button class="btn btn-xs btn-primary">Approve</button></form>
                        <form method="post" action="{{ route('admin.sales.reject', $s) }}" style="display:inline" onsubmit="const n=prompt('Reason shown to the café:','Payment could not be confirmed.'); if(n===null) return false; this.note.value=n;">@csrf<input type="hidden" name="note"><button class="btn btn-xs btn-danger">Reject</button></form>
                    @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="8" class="empty">Nothing here.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $subs->links('admin._pager') }}</div>

<dialog id="dlg-record">
    <form method="post" action="{{ route('admin.sales.store') }}">
        @csrf
        <div class="dlg-head"><h3>Record a payment</h3><button class="btn btn-ghost btn-icon btn-sm" data-close><x-icon name="x"/></button></div>
        <div class="dlg-body stack">
            <div class="field"><label>Café</label>
                <select class="input" name="cafe_id" required>
                    <option value="">Choose…</option>
                    @foreach ($cafes as $c)<option value="{{ $c->id }}" @selected(request('cafe') == $c->id)>{{ $c->name }} (/{{ $c->slug }})</option>@endforeach
                </select></div>
            <div class="grid-2">
                <div class="field"><label>Plan</label>
                    <select class="input" name="plan" id="rec-plan">@foreach ($plans->where('key', '!=', 'trial') as $p)<option value="{{ $p->key }}" data-price="{{ $p->price }}">{{ $p->name }} · {{ number_format($p->price) }}/mo</option>@endforeach</select></div>
                <div class="field"><label>Months</label><input class="input" type="number" name="months" id="rec-months" value="1" min="1" max="36" required></div>
            </div>
            <div class="grid-2">
                <div class="field"><label>Amount received</label><input class="input" type="number" step="0.01" name="amount" id="rec-amount" required></div>
                <div class="field"><label>Method</label><select class="input" name="method">@foreach (\App\Models\Subscription::METHODS as $k => $l)<option value="{{ $k }}">{{ $l }}</option>@endforeach</select></div>
            </div>
            <div class="field"><label>Reference</label><input class="input" name="reference" placeholder="Transaction ID"></div>
            <div class="field"><label>Note</label><input class="input" name="note"></div>
        </div>
        <div class="dlg-foot"><button type="button" class="btn" data-close>Cancel</button><button class="btn btn-primary">Record & activate</button></div>
    </form>
</dialog>
@endsection

@push('scripts')
<script>
(function () {
    const plan = document.getElementById('rec-plan'), months = document.getElementById('rec-months'), amount = document.getElementById('rec-amount');
    const calc = () => { amount.value = (parseFloat(plan.selectedOptions[0]?.dataset.price || 0) * (parseInt(months.value) || 1)).toFixed(2); };
    plan.addEventListener('change', calc); months.addEventListener('input', calc); calc();
    if (location.hash === '#record' || new URLSearchParams(location.search).get('cafe')) document.getElementById('dlg-record').showModal();
})();
</script>
@endpush
