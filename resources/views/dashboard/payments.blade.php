@extends('layouts.panel')
@section('title', 'Payments')

@php
    $colors = ['bank' => '#1D4ED8', 'telebirr' => '#0E9F6E', 'cbe_birr' => '#7E22CE', 'other' => '#475569'];
@endphp

@section('content')
<div class="page-head">
    <div>
        <h1>How customers pay</h1>
        <p>Shown on the Pay tab with copy buttons. We never touch the money.</p>
    </div>
    <button class="btn btn-primary" data-dialog="dlg-pay-new"><x-icon name="plus"/>Add account</button>
</div>

<div class="card">
    @if ($accounts->isEmpty())
        <div class="empty">
            <div class="glyph"><x-icon name="wallet"/></div>
            <h3>No accounts yet</h3>
            <p>Add your bank accounts, Telebirr or CBE Birr so customers stop asking at the counter.</p>
            <button class="btn btn-primary mt-4" data-dialog="dlg-pay-new"><x-icon name="plus"/>Add first account</button>
        </div>
    @else
        <ul class="list">
            @foreach ($accounts as $a)
                <li class="{{ $a->visible ? '' : 'is-off' }}" style="{{ $a->visible ? '' : 'opacity:.55' }}">
                    <div class="reorder">
                        <form method="post" action="{{ route('dashboard.payments.move', $a) }}">@csrf<input type="hidden" name="dir" value="up"><button {{ $loop->first ? 'disabled' : '' }}><x-icon name="up" class="sm"/></button></form>
                        <form method="post" action="{{ route('dashboard.payments.move', $a) }}">@csrf<input type="hidden" name="dir" value="down"><button {{ $loop->last ? 'disabled' : '' }}><x-icon name="down" class="sm"/></button></form>
                    </div>
                    <span class="pay-badge" style="background:linear-gradient(150deg,{{ $colors[$a->type] ?? '#475569' }},{{ mix_hex($colors[$a->type] ?? '#475569', '#000000', .25) }})">{{ $a->monogram() }}</span>
                    <div class="grow">
                        <b>{{ $a->label() }}</b> @unless ($a->visible)<span class="badge">Hidden</span>@endunless
                        <div class="small muted">{{ $a->account_number }}@if ($a->account_name) · {{ $a->account_name }}@endif</div>
                    </div>
                    <button class="btn btn-sm" data-dialog="dlg-pay-{{ $a->id }}"><x-icon name="edit" class="sm"/>Edit</button>
                </li>

                <dialog id="dlg-pay-{{ $a->id }}">
                    <form method="post" action="{{ route('dashboard.payments.update', $a) }}">
                        @csrf @method('put')
                        <div class="dlg-head"><h3>Edit account</h3><button class="btn btn-ghost btn-icon btn-sm" data-close><x-icon name="x"/></button></div>
                        <div class="dlg-body stack">
                            @include('dashboard._payment_fields', ['a' => $a])
                            <label class="switch"><input type="checkbox" name="visible" value="1" @checked($a->visible)><span class="track"></span>Show on menu</label>
                        </div>
                        <div class="dlg-foot" style="justify-content:space-between">
                            <button type="submit" form="del-pay-{{ $a->id }}" class="btn btn-danger btn-sm"><x-icon name="trash" class="sm"/>Remove</button>
                            <div class="row"><button type="button" class="btn" data-close>Cancel</button><button class="btn btn-primary">Save</button></div>
                        </div>
                    </form>
                    <form id="del-pay-{{ $a->id }}" method="post" action="{{ route('dashboard.payments.destroy', $a) }}" data-confirm="Remove this account?">@csrf @method('delete')</form>
                </dialog>
            @endforeach
        </ul>
    @endif
</div>

<p class="small muted mt-4">Customers see: “After paying, show your receipt to the cashier.”</p>

<dialog id="dlg-pay-new">
    <form method="post" action="{{ route('dashboard.payments.store') }}">
        @csrf
        <div class="dlg-head"><h3>Add account</h3><button class="btn btn-ghost btn-icon btn-sm" data-close><x-icon name="x"/></button></div>
        <div class="dlg-body stack">@include('dashboard._payment_fields', ['a' => null])</div>
        <div class="dlg-foot"><button type="button" class="btn" data-close>Cancel</button><button class="btn btn-primary">Add account</button></div>
    </form>
</dialog>
@endsection
