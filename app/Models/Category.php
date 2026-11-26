<?php

namespace App\Models;

use App\Models\Concerns\FlushesCafeMenu;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use FlushesCafeMenu;

    protected $fillable = ['name', 'icon', 'sort_order', 'visible'];

    protected $casts = ['name' => 'array', 'visible' => 'boolean'];

    public function cafe(): BelongsTo
    {
        return $this->belongsTo(Cafe::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(Item::class)->orderBy('sort_order')->orderBy('id');
    }
}
