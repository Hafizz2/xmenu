@extends('errors.layout')
@section('title', 'Not allowed')
@section('code', '405 · Not allowed')
@section('icon')<svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="8.5"/><path d="m6 6 12 12"/></svg>@endsection
@section('heading', 'That action isn’t allowed here')
@section('message', 'Go back and use the buttons on the page.')
