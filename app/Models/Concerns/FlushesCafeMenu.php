<?php

namespace App\Models\Concerns;

use App\Models\Cafe;

/**
 * Any change to a menu-related row clears that café's cached menu page,
 * so the next scan gets fresh prices.
 */
trait FlushesCafeMenu
{
    public static function bootFlushesCafeMenu(): void
    {
        $flush = function ($model) {
            if ($model->cafe_id) {
                Cafe::flushMenuFor($model->cafe_id);
            }
        };
        static::saved($flush);
        static::deleted($flush);
    }
}
