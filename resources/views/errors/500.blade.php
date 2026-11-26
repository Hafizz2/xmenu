@extends('errors.layout')
@section('title', 'Something went wrong')
@section('code', '500 · Server error')
@section('icon')<svg viewBox="0 0 24 24"><path d="M12 3.5 21 19.5H3z"/><path d="M12 10v4M12 17h.01"/></svg>@endsection
@section('heading', 'Something went wrong on our side')
@section('message', 'It’s not you. We’ve been notified and are fixing it. Please try again in a minute.')
@section('message_am', 'ችግሩ ከእኛ ነው። እባክዎ ከትንሽ ጊዜ በኋላ እንደገና ይሞክሩ።')
@section('buttons')
    <a class="btn primary" href="javascript:location.reload()">Try again</a>
    <a class="btn soft" href="/">Go to the home page</a>
@endsection
