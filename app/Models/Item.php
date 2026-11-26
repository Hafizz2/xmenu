<?php

namespace App\Models;

use App\Models\Concerns\FlushesCafeMenu;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Item extends Model
{
    use FlushesCafeMenu;

    protected $fillable = [
        'category_id', 'name', 'description', 'price', 'image', 'tags',
        'variants', 'addons', 'sort_order', 'visible', 'sold_out',
    ];

    protected $casts = [
        'name' => 'array',
        'description' => 'array',
        'tags' => 'array',
        'variants' => 'array',
        'addons' => 'array',
        'price' => 'float',
        'visible' => 'boolean',
        'sold_out' => 'boolean',
    ];

    public function cafe(): BelongsTo
    {
        return $this->belongsTo(Cafe::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /** Lowest price across base and variants, for "from" display. */
    public function fromPrice(): float
    {
        $prices = collect($this->variants ?? [])->pluck('price')->filter(fn ($p) => $p !== null && $p !== '')->map(fn ($p) => (float) $p);

        return $prices->isEmpty() ? $this->price : $prices->min();
    }

    public function hasVariants(): bool
    {
        return count($this->variants ?? []) > 0;
    }
}
