@extends('errors.layout')
@section('title', 'Please sign in')
@section('code', '401 · Sign in needed')
@section('icon')<svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/></svg>@endsection
@section('heading', 'Please sign in first')
@section('message', 'This page is only for café owners. Sign in to continue.')
@section('buttons')
    <a class="btn primary" href="/login">Sign in</a>
    <a class="btn soft" href="/">Go to the home page</a>
@endsection
