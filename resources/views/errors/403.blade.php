@extends('errors.layout')
@section('title', 'No access')
@section('code', '403 · No access')
@section('icon')<svg viewBox="0 0 24 24"><rect x="4.5" y="10.5" width="15" height="10" rx="2.5"/><path d="M8 10.5V7.5a4 4 0 0 1 8 0v3"/></svg>@endsection
@section('heading', 'This page isn’t for your account')
@section('message', 'You’re signed in, but this page belongs to someone else. If you think that’s a mistake, sign in with the right account.')
@section('buttons')
    <a class="btn primary" href="/dashboard">Go to my dashboard</a>
    <a class="btn soft" href="/login">Sign in with another account</a>
@endsection
