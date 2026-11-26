@extends('layouts.panel')
@section('title', 'Games')

@section('content')
<div class="page-head">
    <div>
        <h1>Games</h1>
        <p>Games customers play while they wait for their food. No ads, nothing to install.</p>
    </div>
</div>

<form method="post">
    @csrf @method('put')
    <div class="card card-pad">
        <label class="switch"><input type="checkbox" name="games_enabled" value="1" @checked($cafe->games_enabled)><span class="track"></span><span><b>Show the Games tab</b><br><span class="small muted">Turn off to hide all games from your menu.</span></span></label>
    </div>

    <div class="card mt-4">
        <div class="card-head"><h2>Game library</h2><span class="tiny muted">{{ $games->count() }} games</span></div>
        <ul class="list">
            @foreach ($games as $g)
                <li>
                    <span class="game-thumb" style="background:linear-gradient(150deg,{{ $g->color }},{{ mix_hex($g->color ?? '#6D28D9', '#000000', .3) }})">{!! $g->artSvg() !!}</span>
                    <div class="grow">
                        <b>{{ tr($g->name, 'en') }}</b>
                        <div class="small muted">{{ tr($g->description, 'en') }}</div>
                        <div class="row mt-1" style="gap:6px;flex-wrap:wrap">
                            <span class="badge plain">{{ $g->players == 2 ? '2 players' : '1 player' }}</span>
                            <span class="badge plain">{{ \App\Models\Game::MODES[$g->mode] ?? $g->mode }}</span>
                            @if ($g->has_ai)<span class="badge info plain">vs computer</span>@endif
                        </div>
                    </div>
                    <a href="{{ $g->url() }}" target="_blank" class="btn btn-sm btn-ghost"><x-icon name="play" class="sm"/>Try</a>
                    <label class="switch" title="Show on my menu"><input type="checkbox" name="games[]" value="{{ $g->id }}" @checked(! in_array($g->id, $cafe->disabled_games ?? []))><span class="track"></span></label>
                </li>
            @endforeach
        </ul>
    </div>
    <div class="sticky-save"><button class="btn btn-primary"><x-icon name="check" class="sm"/>Save</button></div>
</form>
@endsection
