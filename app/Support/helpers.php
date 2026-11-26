<?php

use Illuminate\Support\Facades\Storage;

if (! function_exists('tr')) {
    /**
     * Pick a translation from a {en, am} array, falling back to any filled language.
     */
    function multilang(): bool
    {
        return count(config('xmenu.languages')) > 1;
    }
}

if (! function_exists('tr')) {
    function tr($value, ?string $lang = null): string
    {
        if (is_string($value)) {
            return $value;
        }
        if (! is_array($value)) {
            return '';
        }
        $lang ??= app()->getLocale();
        if (! empty($value[$lang])) {
            return $value[$lang];
        }
        foreach ($value as $v) {
            if (! empty($v)) {
                return $v;
            }
        }

        return '';
    }
}

if (! function_exists('img_url')) {
    /**
     * Images are stored as a base path; each size lives at {base}-{width}.webp.
     */
    function img_url(?string $base, int $width = 640, string $ext = 'webp'): ?string
    {
        if (! $base) {
            return null;
        }

        return Storage::disk('public')->url("{$base}-{$width}.{$ext}");
    }
}

if (! function_exists('money')) {
    function money($amount): string
    {
        $amount = (float) $amount;

        return fmod($amount, 1.0) == 0.0
            ? number_format($amount, 0)
            : number_format($amount, 2);
    }
}

if (! function_exists('ink_on')) {
    /** Black or white text, whichever reads better on the given hex colour. */
    function ink_on(string $hex): string
    {
        [$r, $g, $b] = hex_rgb($hex);
        $lin = fn ($c) => ($c /= 255) <= 0.03928 ? $c / 12.92 : (($c + 0.055) / 1.055) ** 2.4;
        $l = 0.2126 * $lin($r) + 0.7152 * $lin($g) + 0.0722 * $lin($b);

        return $l > 0.45 ? '#111111' : '#FFFFFF';
    }
}

if (! function_exists('hex_rgb')) {
    function hex_rgb(string $hex): array
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }

        return [hexdec(substr($hex, 0, 2)), hexdec(substr($hex, 2, 2)), hexdec(substr($hex, 4, 2))];
    }
}

if (! function_exists('mix_hex')) {
    /** Mix $hex with $with by $amount (0..1 of $with). */
    function mix_hex(string $hex, string $with, float $amount): string
    {
        [$r1, $g1, $b1] = hex_rgb($hex);
        [$r2, $g2, $b2] = hex_rgb($with);
        $m = fn ($a, $b) => (int) round($a + ($b - $a) * $amount);

        return sprintf('#%02X%02X%02X', $m($r1, $r2), $m($g1, $g2), $m($b1, $b2));
    }
}
