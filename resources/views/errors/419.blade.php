@extends('errors.layout')
@section('title', 'Page expired')
@section('code', '419 · Page expired')
@section('icon')<svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="8.5"/><path d="M12 7.5V12l3 2"/></svg>@endsection
@section('heading', 'That page was open too long')
@section('message', 'For your safety, forms expire after a while. Go back, refresh the page and try again. Nothing was saved.')
@section('buttons')
    <a class="btn primary" href="javascript:history.back()">Go back and try again</a>
    <a class="btn soft" href="/">Go to the home page</a>
@endsection
