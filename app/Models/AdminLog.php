<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminLog extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = ['user_id', 'cafe_id', 'action', 'details', 'ip'];

    protected $casts = ['details' => 'array'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cafe(): BelongsTo
    {
        return $this->belongsTo(Cafe::class);
    }

    public static function record(string $action, ?int $cafeId = null, array $details = []): void
    {
        static::create([
            'user_id' => auth()->id(),
            'cafe_id' => $cafeId,
            'action' => $action,
            'details' => $details ?: null,
            'ip' => request()->ip(),
        ]);
    }
}
