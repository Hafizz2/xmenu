<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    public const UPDATED_AT = null;

    public const TYPES = [
        'menu_view', 'category_view', 'item_open', 'search', 'filter_used', 'language_switch',
        'pay_copy', 'wifi_copy', 'call_tap', 'map_tap', 'game_open', 'game_close',
    ];

    protected $fillable = ['cafe_id', 'table_no', 'type', 'item_id', 'game_id', 'value'];
}
