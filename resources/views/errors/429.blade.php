@extends('errors.layout')
@section('title', 'Slow down')
@section('code', '429 · Too many tries')
@section('icon')<svg viewBox="0 0 24 24"><path d="M12 3v3M12 18v3M4.2 6.2l2.1 2.1M17.7 15.7l2.1 2.1M3 12h3M18 12h3"/><circle cx="12" cy="12" r="3"/></svg>@endsection
@section('heading', 'Too many tries in a short time')
@section('message', 'Please wait a minute, then try again. This protects your account from people guessing passwords.')
@section('buttons')
    <a class="btn primary" href="javascript:history.back()">Go back</a>
@endsection
