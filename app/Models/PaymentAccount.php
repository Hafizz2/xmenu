<?php

namespace App\Models;

use App\Models\Concerns\FlushesCafeMenu;
use Illuminate\Database\Eloquent\Model;

class PaymentAccount extends Model
{
    use FlushesCafeMenu;

    protected $fillable = ['type', 'provider', 'account_name', 'account_number', 'sort_order', 'visible'];

    protected $casts = ['visible' => 'boolean'];

    public function label(): string
    {
        return match ($this->type) {
            'bank' => $this->provider ?: 'Bank',
            'telebirr' => 'Telebirr',
            'cbe_birr' => 'CBE Birr',
            default => $this->provider ?: 'Wallet',
        };
    }

    /** Short monogram for the badge shown next to the account. */
    public function monogram(): string
    {
        return match ($this->type) {
            'telebirr' => 'tb',
            'cbe_birr' => 'CBE',
            default => collect(preg_split('/\s+/', $this->label()))
                ->reject(fn ($w) => in_array(strtolower($w), ['of', 'bank', 'the', 'international']))
                ->map(fn ($w) => mb_substr($w, 0, 1))->take(3)->implode('') ?: 'B',
        };
    }
}
