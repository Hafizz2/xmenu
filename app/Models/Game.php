<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    protected $fillable = [
        'slug', 'name', 'description', 'source_url', 'hosted', 'license', 'players', 'mode', 'has_ai',
        'size_kb', 'emoji', 'art', 'color', 'sort_order', 'active',
    ];

    protected $casts = ['name' => 'array', 'description' => 'array', 'hosted' => 'boolean', 'active' => 'boolean', 'has_ai' => 'boolean'];

    public const ARTS = ['tictactoe' => 'Grid (X / O)', 'connect4' => 'Discs', 'memory' => 'Cards', 'reaction' => 'Lightning', 'dots' => 'Dots', 'generic' => 'Controller'];

    public const MODES = ['pass_and_play' => 'Pass & play', 'split_screen' => 'Split screen', 'solo' => 'Solo', 'two_phones' => 'Two phones'];

    protected static function booted(): void
    {
        // A game change affects every menu page; clear all cached pages.
        $flush = fn () => \Illuminate\Support\Facades\Cache::flush();
        static::saved($flush);
        static::deleted($flush);
    }

    public function url(): string
    {
        return $this->hosted ? asset($this->source_url) : $this->source_url;
    }

    /** Illustration for the game card: simple geometric SVG drawn in white on the game colour. */
    public function artSvg(): string
    {
        $w = 'stroke="#fff" stroke-width="5" stroke-linecap="round" fill="none"';

        $body = match ($this->art) {
            'tictactoe' => '<path d="M70 30v100M110 30v100M40 60h100M40 100h100" '.$w.' opacity=".55"/><path d="M48 38l14 14M62 38 48 52" '.$w.'/><circle cx="90" cy="80" r="9" '.$w.'/><path d="M118 108l14 14M132 108l-14 14" '.$w.'/><circle cx="55" cy="115" r="9" '.$w.' opacity=".55"/>',
            'connect4' => '<rect x="34" y="30" width="112" height="96" rx="14" fill="#fff" opacity=".18"/>'
                .collect([[52, 48], [76, 48], [100, 48], [124, 48], [52, 72], [76, 72], [100, 72], [124, 72], [52, 96], [76, 96], [100, 96], [124, 96], [52, 118], [76, 118], [100, 118], [124, 118]])
                    ->map(fn ($p, $i) => '<circle cx="'.$p[0].'" cy="'.($p[1] - 6).'" r="8" fill="'.(in_array($i, [9, 13, 14, 15]) ? '#FDE047' : (in_array($i, [10, 11, 12, 6]) ? '#fff' : '#ffffff33')).'"/>')->implode(''),
            'memory' => '<rect x="38" y="40" width="44" height="58" rx="8" fill="#fff" opacity=".25" transform="rotate(-10 60 69)"/><rect x="68" y="34" width="44" height="58" rx="8" fill="#fff"/><path d="M90 52l4 8 9 1-6.5 6 1.5 9-8-4.5-8 4.5 1.5-9-6.5-6 9-1z" fill="#16A34A"/><rect x="98" y="48" width="44" height="58" rx="8" fill="#fff" opacity=".5" transform="rotate(10 120 77)"/>',
            'reaction' => '<circle cx="90" cy="80" r="44" fill="#fff" opacity=".15"/><circle cx="90" cy="80" r="30" fill="#fff" opacity=".25"/><path d="M96 46 74 86h16l-6 28 24-42H92z" fill="#fff"/>',
            'dots' => collect(range(0, 3))->flatMap(fn ($r) => collect(range(0, 3))->map(fn ($c) => '<circle cx="'.(50 + $c * 27).'" cy="'.(40 + $r * 27).'" r="4.5" fill="#fff"/>'))->implode('').'<path d="M50 40h27v27H50zM104 67h27" '.$w.'/>',
            default => '<rect x="40" y="52" width="100" height="56" rx="28" '.$w.'/><path d="M68 70v20M58 80h20" '.$w.'/><circle cx="110" cy="76" r="4" fill="#fff"/><circle cx="122" cy="86" r="4" fill="#fff"/>',
        };

        return '<svg viewBox="0 0 180 160" aria-hidden="true" preserveAspectRatio="xMidYMid meet">'.$body.'</svg>';
    }
}
