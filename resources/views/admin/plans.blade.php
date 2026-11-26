@extends('layouts.admin')
@section('title', 'Plans & pricing')

@section('content')
<div class="page-head"><div><h1>Plans & pricing</h1><p>Prices shown to café owners on their billing page. Changes apply to new payments.</p></div></div>

<div class="grid-2" style="align-items:start">
    @foreach ($plans as $p)
        <form method="post" action="{{ route('admin.plans.update', $p) }}" class="card card-pad">
            @csrf @method('put')
            <div class="row between"><div class="section-title">{{ $p->name }} <span class="tiny muted mono">{{ $p->key }}</span></div>
                <label class="switch"><input type="checkbox" name="active" value="1" @checked($p->active)><span class="track"></span><span class="small">Offered</span></label></div>
            <div class="grid-2 mt-3">
                <div class="field"><label>Name</label><input class="input" name="name" value="{{ $p->name }}" required></div>
                <div class="field"><label>Price per month</label><div class="input-group"><input class="input" name="price" type="number" step="0.01" min="0" value="{{ $p->price + 0 }}" required><input class="input" name="currency" value="{{ $p->currency }}" style="max-width:80px" required></div></div>
            </div>
            <div class="field mt-3"><label>Short description</label><input class="input" name="tagline" value="{{ $p->tagline }}"></div>
            <div class="field mt-3"><label>Table QR limit</label><input class="input" name="max_tables" type="number" min="1" value="{{ $p->max_tables }}" placeholder="Unlimited"></div>
            <div class="field mt-3"><label>Features (one per line)</label><textarea class="input" name="features" rows="4">{{ implode("\n", $p->features ?? []) }}</textarea></div>
            <button class="btn btn-primary mt-3">Save {{ $p->name }}</button>
        </form>
    @endforeach
</div>
@endsection
