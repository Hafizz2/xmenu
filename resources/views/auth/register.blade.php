@extends('layouts.auth')
@section('title', 'Create your menu')

@section('content')
<h1>Create your café’s menu</h1>
<p class="muted">Free for {{ \App\Models\Setting::get('trial_days', config('xmenu.trial_days')) }} days. No card needed.</p>

<form method="post" action="{{ route('register') }}" class="stack mt-6 card card-pad glass" style="background:var(--glass-strong)">
    @csrf
    <div class="field">
        <label>Café name</label>
        <input class="input @error('cafe_name') is-invalid @enderror" name="cafe_name" value="{{ old('cafe_name') }}" required autofocus placeholder="Tomoca Bole">
        @error('cafe_name')<div class="error">{{ $message }}</div>@enderror
    </div>
    <div class="grid-2">
        <div class="field">
            <label>Your name</label>
            <input class="input @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name">
            @error('name')<div class="error">{{ $message }}</div>@enderror
        </div>
        <div class="field">
            <label>Phone <span class="faint">(optional)</span></label>
            <input class="input" name="phone" type="tel" value="{{ old('phone') }}" autocomplete="tel" placeholder="+251 9…">
        </div>
    </div>
    <div class="field">
        <label>Email</label>
        <input class="input @error('email') is-invalid @enderror" name="email" type="email" value="{{ old('email') }}" required autocomplete="email">
        @error('email')<div class="error">{{ $message }}</div>@enderror
    </div>
    <div class="grid-2">
        <div class="field">
            <label>Password</label>
            <input class="input @error('password') is-invalid @enderror" name="password" type="password" required minlength="8" autocomplete="new-password">
            @error('password')<div class="error">{{ $message }}</div>@enderror
        </div>
        <div class="field">
            <label>Repeat password</label>
            <input class="input" name="password_confirmation" type="password" required autocomplete="new-password">
        </div>
    </div>
    <button class="btn btn-brand btn-lg btn-block">Create my menu<svg class="i sm" viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"/></svg></button>
    <p class="small muted" style="text-align:center">Already have an account? <a href="{{ route('login') }}">Log in</a></p>
</form>
@endsection
