<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminLog;
use App\Models\Game;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/** Game library, audit log and platform settings. */
class PlatformController extends Controller
{
    public function games()
    {
        return view('admin.games', ['games' => Game::orderBy('sort_order')->get()]);
    }

    public function storeGame(Request $request)
    {
        $data = $request->validate([
            'name_en' => ['required', 'string', 'max:60'],
            'name_am' => ['nullable', 'string', 'max:60'],
            'source_url' => ['required', 'string', 'max:500'],
            'license' => ['nullable', 'string', 'max:60'],
            'players' => ['required', 'integer', 'in:1,2'],
            'mode' => ['required', Rule::in(['pass_and_play', 'split_screen', 'solo', 'two_phones'])],
            'art' => ['nullable', 'in:'.implode(',', array_keys(Game::ARTS))],
            'description_en' => ['nullable', 'string', 'max:140'],
            'color' => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
        ]);
        $game = Game::create([
            'slug' => Str::slug($data['name_en']).'-'.Str::lower(Str::random(4)),
            'name' => $this->pair($data, 'name'),
            'source_url' => $data['source_url'],
            'hosted' => ! preg_match('#^https?://#', $data['source_url']),
            'license' => $data['license'] ?? null,
            'players' => $data['players'],
            'mode' => $data['mode'],
            'art' => $data['art'] ?? 'generic',
            'description' => ['en' => $data['description_en'] ?? null, 'am' => null],
            'color' => $data['color'] ?? '#6D28D9',
            'sort_order' => (int) Game::max('sort_order') + 1,
            'active' => true,
        ]);
        AdminLog::record('game.create', null, ['game' => $game->slug]);

        return back()->with('ok', 'Game added and live for all cafés.');
    }

    public function toggleGame(Game $game)
    {
        $game->update(['active' => ! $game->active]);
        AdminLog::record($game->active ? 'game.enable' : 'game.disable', null, ['game' => $game->slug]);

        return back()->with('ok', tr($game->name, 'en').($game->active ? ' is on.' : ' is off.'));
    }

    public function logs(Request $request)
    {
        $action = $request->query('action');
        $logs = AdminLog::with(['user', 'cafe'])
            ->when($action, fn ($q) => $q->where('action', 'like', $action.'%'))
            ->when($request->query('cafe'), fn ($q, $c) => $q->where('cafe_id', $c))
            ->latest('id')->paginate(50)->withQueryString();
        $groups = ['cafe', 'content', 'user', 'sales', 'plan', 'game', 'settings'];

        return view('admin.logs', compact('logs', 'action', 'groups'));
    }

    public function settings()
    {
        return view('admin.settings', [
            'accounts' => Setting::get('billing_accounts', []),
            'support' => Setting::get('support', []),
            'trialDays' => Setting::get('trial_days', config('xmenu.trial_days')),
        ]);
    }

    public function updateSettings(Request $request)
    {
        $data = $request->validate([
            'trial_days' => ['required', 'integer', 'min:0', 'max:365'],
            'support.phone' => ['nullable', 'string', 'max:40'],
            'support.email' => ['nullable', 'email', 'max:150'],
            'support.telegram' => ['nullable', 'string', 'max:100'],
            'accounts' => ['nullable', 'array', 'max:10'],
            'accounts.*.label' => ['nullable', 'string', 'max:80'],
            'accounts.*.name' => ['nullable', 'string', 'max:100'],
            'accounts.*.number' => ['nullable', 'string', 'max:80'],
        ]);
        Setting::put('trial_days', (int) $data['trial_days']);
        Setting::put('support', array_filter($data['support'] ?? []));
        Setting::put('billing_accounts', collect($data['accounts'] ?? [])->filter(fn ($a) => ! empty($a['number']))->values()->all());
        AdminLog::record('settings.update');

        return back()->with('ok', 'Platform settings saved.');
    }
}
