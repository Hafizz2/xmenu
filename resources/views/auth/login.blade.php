@extends('layouts.auth')
@section('title', 'Log in')

@section('content')
<h1>Welcome back</h1>
<p class="muted">Log in to update your menu.</p>

<form method="post" action="{{ route('login') }}" class="stack mt-6 card card-pad glass" style="background:var(--glass-strong)">
    @csrf
    <div class="field">
        <label>Email</label>
        <input class="input @error('email') is-invalid @enderror" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="email">
        @error('email')<div class="error">{{ $message }}</div>@enderror
    </div>
    <div class="field">
        <label>Password</label>
        <input class="input" name="password" type="password" required autocomplete="current-password">
    </div>
    <label class="row small"><input type="checkbox" name="remember" value="1" checked> Keep me logged in</label>
    <button class="btn btn-primary btn-lg btn-block">Log in</button>
    <p class="small muted" style="text-align:center">New here? <a href="{{ route('register') }}">Create your menu free</a></p>
    <p class="tiny faint" style="text-align:center">Forgot your password? Contact Xmenu support and we’ll reset it.</p>
</form>
@endsection
