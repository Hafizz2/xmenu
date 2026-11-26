<?php

namespace App\Http\Controllers;

use App\Models\Cafe;
use App\Models\Event;
use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PublicMenuController extends Controller
{
    public function show(Request $request, string $slug)
    {
        $langs = array_keys(config('xmenu.languages'));
        $lang = $request->query('lang');
        $setCookie = false;
        if (in_array($lang, $langs, true)) {
            $setCookie = true;
        } else {
            $lang = $request->cookie('xm_lang');
            $lang = in_array($lang, $langs, true) ? $lang : null;
        }

        $key = Cafe::cacheKey($slug, $lang ?? '_');
        $html = Cache::remember($key, now()->addDay(), function () use ($slug, $lang) {
            $cafe = Cafe::where('slug', $slug)->first();
            if (! $cafe) {
                return '';
            }

            return $this->render($cafe, $lang ?? (in_array($cafe->default_lang, array_keys(config('xmenu.languages')), true) ? $cafe->default_lang : 'en'));
        });

        if ($html === '') {
            Cache::forget($key);
            abort(404);
        }

        $response = response($html)
            ->header('Cache-Control', 'public, max-age=30')
            ->header('Vary', 'Cookie');
        if ($setCookie) {
            $response->withCookie(cookie()->forever('xm_lang', $lang, null, null, null, false));
        }

        return $response;
    }

    /** Live preview for the Look & colours page. Signed-in only, never cached, theme overrides allowed. */
    public function preview(Request $request)
    {
        $cafe = $request->attributes->get('cafe');
        $langs = array_keys(config('xmenu.languages'));
        $lang = in_array($request->query('lang'), $langs, true) ? $request->query('lang') : (in_array($cafe->default_lang, $langs, true) ? $cafe->default_lang : 'en');
        $overrides = $request->only(array_merge(['color', 'layout', 'header', 'font', 'radius', 'mode', 'surface'], array_keys(config('xmenu.theme_switches'))));

        return response($this->render($cafe, $lang, $overrides, true))->header('Cache-Control', 'no-store, private');
    }

    private function render(Cafe $cafe, string $lang, array $overrides = [], bool $preview = false): string
    {
        app()->setLocale($lang);

        if (! $cafe->isLive()) {
            return view('menu.unavailable', compact('cafe'))->render();
        }

        $cafe->load([
            'categories' => fn ($q) => $q->where('visible', true),
            'categories.items' => fn ($q) => $q->where('visible', true),
            'paymentAccounts' => fn ($q) => $q->where('visible', true),
            'openingHours',
        ]);

        $categories = $cafe->categories
            ->map(function ($cat) {
                // Sold-out items sink to the end of their category.
                $cat->setRelation('items', $cat->items->sortBy(fn ($i) => [$i->sold_out ? 1 : 0, $i->sort_order, $i->id])->values());

                return $cat;
            })
            ->filter(fn ($cat) => $cat->items->isNotEmpty())
            ->values();

        $usedTags = $categories->flatMap(fn ($c) => $c->items->flatMap(fn ($i) => $i->tags ?? []))->unique()->values();
        $tags = collect(config('xmenu.tags'))->only($usedTags->all());

        $games = collect();
        if ($cafe->games_enabled) {
            $disabled = $cafe->disabled_games ?? [];
            $games = Game::where('active', true)->orderBy('sort_order')->get()
                ->reject(fn ($g) => in_array($g->id, $disabled));
        }

        $theme = $cafe->resolvedTheme($overrides);
        $ui = config("xmenu.ui.$lang");
        $hours = $cafe->openingHours->mapWithKeys(fn ($h) => [$h->day_of_week => [
            'o' => $h->opens_at ? substr($h->opens_at, 0, 5) : null,
            'c' => $h->closes_at ? substr($h->closes_at, 0, 5) : null,
            'x' => $h->closed,
        ]]);

        return view('menu.show', compact('cafe', 'categories', 'tags', 'games', 'theme', 'ui', 'lang', 'hours', 'preview'))->render();
    }

    public function track(Request $request)
    {
        $data = $request->validate([
            'c' => ['required', 'integer'],
            'type' => ['required', 'string', 'in:'.implode(',', Event::TYPES)],
            't' => ['nullable', 'string', 'max:20'],
            'item' => ['nullable', 'integer'],
            'game' => ['nullable', 'integer'],
            'v' => ['nullable', 'string', 'max:120'],
        ]);

        if (Cache::remember('cafe_exists:'.$data['c'], 3600, fn () => Cafe::whereKey($data['c'])->exists())) {
            Event::create([
                'cafe_id' => $data['c'],
                'table_no' => $data['t'] ?? null,
                'type' => $data['type'],
                'item_id' => $data['item'] ?? null,
                'game_id' => $data['game'] ?? null,
                'value' => $data['v'] ?? null,
            ]);
        }

        return response()->noContent();
    }
}
