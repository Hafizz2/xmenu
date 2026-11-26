@extends('errors.layout')
@section('title', 'Back soon')
@section('code', '503 · Updating')
@section('icon')<svg viewBox="0 0 24 24"><path d="M14.7 6.3a4 4 0 0 0-5.4 5.4L3.5 17.5l3 3 5.8-5.8a4 4 0 0 0 5.4-5.4l-2.5 2.5-2.5-.5-.5-2.5z"/></svg>@endsection
@section('heading', 'We’re making Xmenu better')
@section('message', 'A quick update is in progress. Menus will be back in a few minutes.')
@section('message_am', 'ማሻሻያ እየተደረገ ነው። በጥቂት ደቂቃዎች ውስጥ እንመለሳለን።')
@section('buttons')
    <a class="btn primary" href="javascript:location.reload()">Try again</a>
@endsection
