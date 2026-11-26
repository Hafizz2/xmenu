<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class Cafe extends Model
{
    protected $guarded = ['id', 'user_id', 'plan', 'status', 'trial_ends_at', 'paid_until', 'admin_notes'];

    protected $casts = [
        'tagline' => 'array',
        'socials' => 'array',
        'theme' => 'array',
        'disabled_games' => 'array',
        'qr_settings' => 'array',
        'vat_included' => 'boolean',
        'games_enabled' => 'boolean',
        'service_charge_pct' => 'float',
        'trial_ends_at' => 'datetime',
        'paid_until' => 'datetime',
    ];

    public const STATUSES = ['trial', 'active', 'overdue', 'suspended'];

    public const RESERVED_SLUGS = [
        'login', 'logout', 'register', 'dashboard', 'admin', 'api', 'e', 'games', 'storage',
        'css', 'js', 'img', 'build', 'up', 'password', 'demo', 'about', 'pricing', 'terms', 'privacy',
        'credits', 'legal', 'cafe', 'cafes', 'menu', 'menus', 'help', 'support', 'contact', 'blog', 'home', 'settings',
        'billing', 'account', 'xmenu', 'xvalue', 'www', 'mail', 'app', 'static', 'assets', 'vendor', 'favicon', 'sitemap', 'robots',
    ];

    protected static function booted(): void
    {
        static::saved(function (Cafe $cafe) {
            $cafe->flushMenu();
            if ($cafe->wasChanged('slug')) {
                static::forgetSlug($cafe->getOriginal('slug'));
            }
        });
        static::deleted(fn (Cafe $cafe) => $cafe->flushMenu());
    }

    // ---------- Relations ----------
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class)->orderBy('sort_order')->orderBy('id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }

    public function paymentAccounts(): HasMany
    {
        return $this->hasMany(PaymentAccount::class)->orderBy('sort_order')->orderBy('id');
    }

    public function openingHours(): HasMany
    {
        return $this->hasMany(OpeningHour::class)->orderBy('day_of_week');
    }

    public function tables(): HasMany
    {
        return $this->hasMany(CafeTable::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class)->latest();
    }

    /** Status badge colour for admin tables. */
    public function statusTone(): string
    {
        return ['active' => 'ok', 'trial' => 'brand', 'overdue' => 'warn', 'suspended' => 'bad'][$this->status] ?? '';
    }

    // ---------- Theme ----------
    public function themeValue(string $key): string
    {
        return $this->theme[$key] ?? config("xmenu.theme_defaults.$key");
    }

    /** Fully resolved theme with derived colours, with optional preview overrides. */
    public function resolvedTheme(array $overrides = []): array
    {
        $t = array_merge(config('xmenu.theme_defaults'), $this->theme ?? [], array_filter($overrides, fn ($v) => $v !== null && $v !== ''));
        foreach (array_keys(config('xmenu.theme_switches')) as $k) {
            $t[$k] = filter_var($t[$k] ?? true, FILTER_VALIDATE_BOOLEAN);
        }
        if (! preg_match('/^#[0-9a-fA-F]{6}$/', $t['color'])) {
            $t['color'] = config('xmenu.theme_defaults.color');
        }
        foreach (['layout' => 'layouts', 'header' => 'headers', 'font' => 'fonts', 'radius' => 'radii', 'surface' => 'surfaces'] as $k => $list) {
            if (! array_key_exists($t[$k], config("xmenu.$list"))) {
                $t[$k] = config("xmenu.theme_defaults.$k");
            }
        }
        $surface = config("xmenu.surfaces.{$t['surface']}");
        $t['ink'] = ink_on($t['color']);
        $t['soft'] = mix_hex($t['color'], '#FFFFFF', 0.88);
        $t['soft_dark'] = mix_hex($t['color'], '#000000', 0.72);
        // Brand used as text must stay readable on white and on near-black.
        $t['text_light'] = $t['ink'] === '#111111' ? mix_hex($t['color'], '#000000', 0.45) : $t['color'];
        $t['text_dark'] = mix_hex($t['color'], '#FFFFFF', 0.35);
        $t['bg'] = $surface['bg'];
        $t['card'] = $surface['card'];
        $t['line'] = $surface['line'];
        $t['font_stack'] = config("xmenu.fonts.{$t['font']}.stack");
        $t['radius_px'] = config("xmenu.radii.{$t['radius']}.px");

        return $t;
    }

    // ---------- Helpers ----------
    public function menuUrl(?string $table = null): string
    {
        return url($this->slug).($table ? '?t='.urlencode($table) : '');
    }

    public function logoUrl(int $w = 256): ?string
    {
        return img_url($this->logo, $w);
    }

    public function coverUrl(int $w = 1024): ?string
    {
        return img_url($this->cover, $w);
    }

    public function isLive(): bool
    {
        return $this->status !== 'suspended';
    }

    public function trialDaysLeft(): ?int
    {
        if ($this->status !== 'trial' || ! $this->trial_ends_at) {
            return null;
        }

        return max(0, (int) ceil(now()->diffInDays($this->trial_ends_at, false)));
    }

    public static function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'cafe';
        $base = Str::limit($base, 50, '');
        $slug = $base;
        $i = 2;
        while (in_array($slug, self::RESERVED_SLUGS, true)
            || static::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }

    // ---------- Page cache ----------
    public static function cacheKey(string $slug, string $lang): string
    {
        return "menu:{$slug}:{$lang}";
    }

    public function flushMenu(): void
    {
        static::forgetSlug($this->slug);
    }

    public static function forgetSlug(?string $slug): void
    {
        if (! $slug) {
            return;
        }
        foreach ([...array_keys(config('xmenu.languages')), '_'] as $lang) {
            Cache::forget(static::cacheKey($slug, $lang));
        }
    }

    public static function flushMenuFor(int $cafeId): void
    {
        $slug = static::whereKey($cafeId)->value('slug');
        static::forgetSlug($slug);
    }
}
