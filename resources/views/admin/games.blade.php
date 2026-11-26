@extends('layouts.admin')
@section('title', 'Game library')

@section('content')
<div class="page-head">
    <div><h1>Game library</h1><p>Games available to every café. Only add games you’ve reviewed: no ads, gambling or adult content.</p></div>
    <button class="btn btn-primary" data-dialog="dlg-game"><x-icon name="plus" class="sm"/>Add game</button>
</div>

<div class="card">
    <ul class="list">
        @foreach ($games as $g)
            <li style="{{ $g->active ? '' : 'opacity:.55' }};flex-wrap:wrap">
                <span class="game-thumb" style="background:linear-gradient(150deg,{{ $g->color }},{{ mix_hex($g->color ?? '#6D28D9', '#000000', .3) }})">{!! $g->artSvg() !!}</span>
                <div class="grow" style="min-width:200px"><b>{{ tr($g->name, 'en') }}</b> <span class="small muted">{{ $g->name['am'] ?? '' }}</span>
                    <div class="small muted">{{ $g->players }} player{{ $g->players > 1 ? 's' : '' }} · {{ \App\Models\Game::MODES[$g->mode] ?? $g->mode }}{{ $g->has_ai ? ' · vs computer' : '' }} · {{ $g->hosted ? 'self-hosted' : 'external' }} · {{ $g->license ?: 'licence unknown' }}</div></div>
                <a href="{{ $g->url() }}" target="_blank" class="btn btn-sm btn-ghost"><x-icon name="play" class="sm"/>Test</a>
                <form method="post" action="{{ route('admin.games.toggle', $g) }}">@csrf<button class="btn btn-sm">{{ $g->active ? 'Turn off' : 'Turn on' }}</button></form>
            </li>
        @endforeach
    </ul>
</div>

<dialog id="dlg-game">
    <form method="post" action="{{ route('admin.games.store') }}">
        @csrf
        <div class="dlg-head"><h3>Add game</h3><button class="btn btn-ghost btn-icon btn-sm" data-close aria-label="Close"><x-icon name="x"/></button></div>
        <div class="dlg-body stack">
            <div class="grid-2">
                <div class="field"><label>Name (EN)</label><input class="input" name="name_en" required></div>
                <div class="field"><label>Name (አማ)</label><input class="input" name="name_am"></div>
            </div>
            <div class="field"><label>Short description</label><input class="input" name="description_en" maxlength="140"></div>
            <div class="field"><label>URL</label><input class="input" name="source_url" required placeholder="games/my-game/index.html or https://…"><div class="hint">A path under /public for self-hosted games, or a full https link.</div></div>
            <div class="grid-3">
                <div class="field"><label>Players</label><select class="input" name="players"><option value="2">2</option><option value="1">1</option></select></div>
                <div class="field"><label>Mode</label><select class="input" name="mode">@foreach (\App\Models\Game::MODES as $k => $l)<option value="{{ $k }}">{{ $l }}</option>@endforeach</select></div>
                <div class="field"><label>Licence</label><input class="input" name="license" placeholder="MIT"></div>
            </div>
            <div class="grid-2">
                <div class="field"><label>Artwork</label><select class="input" name="art">@foreach (\App\Models\Game::ARTS as $k => $l)<option value="{{ $k }}" @selected($k === 'generic')>{{ $l }}</option>@endforeach</select></div>
                <div class="field"><label>Colour</label><input class="input" type="color" name="color" value="#6D28D9" style="padding:4px"></div>
            </div>
        </div>
        <div class="dlg-foot"><button type="button" class="btn" data-close>Cancel</button><button class="btn btn-primary">Add game</button></div>
    </form>
</dialog>
@endsection
