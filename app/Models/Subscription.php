<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    protected $fillable = ['plan', 'months', 'amount', 'currency', 'method', 'reference', 'receipt', 'status', 'note'];

    protected $casts = [
        'amount' => 'float',
        'period_start' => 'datetime',
        'period_end' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public const METHODS = ['bank' => 'Bank transfer', 'telebirr' => 'Telebirr', 'cbe_birr' => 'CBE Birr', 'cash' => 'Cash', 'other' => 'Other'];

    public function cafe(): BelongsTo
    {
        return $this->belongsTo(Cafe::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Approve: the paid period starts when the current one ends (or now), and the café becomes active.
     */
    public function approve(User $admin): void
    {
        $cafe = $this->cafe;
        $start = $cafe->paid_until && $cafe->paid_until->isFuture() ? $cafe->paid_until->copy() : now();
        $this->period_start = $start;
        $this->period_end = $start->copy()->addMonthsNoOverflow($this->months);
        $this->status = 'approved';
        $this->approved_by = $admin->id;
        $this->approved_at = now();
        $this->save();

        $cafe->paid_until = $this->period_end;
        $cafe->plan = $this->plan;
        $cafe->status = 'active';
        $cafe->save();
    }
}
