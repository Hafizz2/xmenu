@extends('layouts.panel')
@section('title', 'Settings')

@section('content')
<div class="page-head"><div><h1>Settings</h1><p>Your menu link, language and account.</p></div></div>

<form method="post">
    @csrf @method('put')
    <div class="card card-pad">
        <div class="section-title">Menu</div>
        <div class="field mt-4">
            <label>Menu link</label>
            <div class="input-group">
                <span class="addon">{{ preg_replace('#^https?://#', '', url('/')) }}/</span>
                <input class="input @error('slug') is-invalid @enderror" name="slug" value="{{ old('slug', $cafe->slug) }}" required pattern="[a-z0-9]+(-[a-z0-9]+)*">
            </div>
            @error('slug')<div class="error">{{ $message }}</div>@enderror
            <div class="flash warn mt-2" style="margin-bottom:0"><x-icon name="info" class="sm"/>Changing this breaks QR codes you’ve already printed. Download new ones after.</div>
        </div>
        <div class="grid-2 mt-3">
            <div class="field">
                <label>Default language</label>
                <select class="input" name="default_lang">
                    @foreach (config('xmenu.languages') as $k => $l)<option value="{{ $k }}" @selected($cafe->default_lang === $k)>{{ $l }}</option>@endforeach
                </select>
                <div class="hint">Customers can switch any time.</div>
            </div>
            <div class="field">
                <label>Currency</label>
                <input class="input" name="currency" value="{{ old('currency', $cafe->currency) }}" maxlength="10" required>
            </div>
        </div>
    </div>

    <div class="card card-pad mt-4">
        <div class="section-title">Your account</div>
        <div class="grid-2 mt-4">
            <div class="field"><label>Your name</label><input class="input" name="name" value="{{ old('name', $user->name) }}" required></div>
            <div class="field"><label>Phone</label><input class="input" name="phone" type="tel" value="{{ old('phone', $user->phone) }}"></div>
        </div>
        <div class="field mt-3"><label>Email</label><input class="input @error('email') is-invalid @enderror" name="email" type="email" value="{{ old('email', $user->email) }}" required>@error('email')<div class="error">{{ $message }}</div>@enderror</div>
        <button class="btn btn-primary mt-4"><x-icon name="check"/>Save settings</button>
    </div>
</form>

@unless ($managing)
<form method="post" action="{{ route('dashboard.password') }}" class="card card-pad mt-4">
    @csrf @method('put')
    <div class="section-title">Change password</div>
    <div class="grid-3 mt-4">
        <div class="field"><label>Current password</label><input class="input @error('current_password') is-invalid @enderror" type="password" name="current_password" autocomplete="current-password" required>@error('current_password')<div class="error">{{ $message }}</div>@enderror</div>
        <div class="field"><label>New password</label><input class="input @error('password') is-invalid @enderror" type="password" name="password" autocomplete="new-password" required minlength="8">@error('password')<div class="error">{{ $message }}</div>@enderror</div>
        <div class="field"><label>Repeat new password</label><input class="input" type="password" name="password_confirmation" autocomplete="new-password" required></div>
    </div>
    <button class="btn mt-4">Change password</button>
</form>
@endunless

<div class="card card-pad mt-4">
    <div class="section-title">Plan</div>
    <p class="muted mt-1">
        @if ($d = $cafe->trialDaysLeft()) You’re on the free trial: <b>{{ $d }} days left</b>. Everything is unlocked.
        @else You’re on the <b>{{ ucfirst($cafe->plan) }}</b> plan ({{ $cafe->status }}).
        @endif
    </p>
</div>
@endsection
