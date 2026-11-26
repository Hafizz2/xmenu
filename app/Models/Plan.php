<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = ['key', 'name', 'tagline', 'price', 'currency', 'max_tables', 'features', 'sort_order', 'active'];

    protected $casts = ['features' => 'array', 'price' => 'float', 'active' => 'boolean'];
}
