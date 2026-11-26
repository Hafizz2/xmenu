<?php

namespace App\Http\Controllers;

use App\Models\Cafe;
use App\Models\Plan;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class HomeController extends Controller
{
    public function index()
    {
        $demo = Cafe::where('slug', 'demo-cafe')->value('slug');
        $plans = Plan::where('active', true)->where('key', '!=', 'trial')->orderBy('sort_order')->get();
        $trialDays = Setting::get('trial_days', config('xmenu.trial_days'));
        $cafeCount = Cafe::count();

        return view('home', compact('demo', 'plans', 'trialDays', 'cafeCount'));
    }

    /** Privacy, terms and photo credits. */
    public function legal(string $page)
    {
        $credits = $page === 'credits' ? Str::markdown(@file_get_contents(database_path('seeders/demo/CREDITS.md')) ?: '', ['html_input' => 'strip', 'allow_unsafe_links' => false]) : null;

        return response()->view('legal', compact('page', 'credits'))->header('Cache-Control', 'public, max-age=3600');
    }

    /**
     * Sitemap for Google: the public site pages plus every live café menu, so a café can be found by its name.
     * Submit https://xmenu.xvalue.et/sitemap.xml once in Google Search Console.
     */
    public function sitemap()
    {
        $xml = Cache::remember('sitemap.xml', now()->addHour(), function () {
            $today = now()->startOfDay()->toAtomString();
            $urls = [
                [url('/'), $today, 'weekly', '1.0'],
                [route('register'), $today, 'monthly', '0.6'],
                [route('legal', 'privacy'), $today, 'yearly', '0.2'],
                [route('legal', 'terms'), $today, 'yearly', '0.2'],
            ];

            Cafe::whereIn('status', ['trial', 'active', 'overdue'])
                ->whereHas('items', fn ($q) => $q->where('visible', true))
                ->orderBy('id')->select(['id', 'slug', 'updated_at'])
                ->chunk(500, function ($cafes) use (&$urls) {
                    foreach ($cafes as $cafe) {
                        $urls[] = [url($cafe->slug), $cafe->updated_at?->toAtomString(), 'daily', '0.8'];
                    }
                });

            $rows = collect($urls)->map(fn ($u) => '  <url><loc>'.e($u[0]).'</loc>'
                .($u[1] ? '<lastmod>'.$u[1].'</lastmod>' : '')
                .'<changefreq>'.$u[2].'</changefreq><priority>'.$u[3].'</priority></url>');

            return '<?xml version="1.0" encoding="UTF-8"?>'."\n"
                .'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n"
                .$rows->implode("\n")."\n</urlset>\n";
        });

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8', 'Cache-Control' => 'public, max-age=3600']);
    }
}
