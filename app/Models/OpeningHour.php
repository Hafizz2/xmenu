<?php

namespace App\Models;

use App\Models\Concerns\FlushesCafeMenu;
use Illuminate\Database\Eloquent\Model;

class OpeningHour extends Model
{
    use FlushesCafeMenu;

    public $timestamps = false;

    protected $fillable = ['day_of_week', 'opens_at', 'closes_at', 'closed'];

    protected $casts = ['closed' => 'boolean'];
}
