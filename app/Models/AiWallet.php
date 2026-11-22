<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * Dompet kredit SINA AI milik sebuah mitra (database pusat).
 */
class AiWallet extends Model
{
    protected $connection = 'central';

    protected $table = 'ai_wallets';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'mitra_id',
        'balance_credits',
        'granted_credits',
        'topped_up_credits',
        'used_credits',
        'period_start',
        'period_end',
        'last_granted_at',
        'last_topped_up_at',
    ];

    protected $casts = [
        'balance_credits'    => 'integer',
        'granted_credits'    => 'integer',
        'topped_up_credits'  => 'integer',
        'used_credits'       => 'integer',
        'period_start'       => 'datetime',
        'period_end'         => 'datetime',
        'last_granted_at'    => 'datetime',
        'last_topped_up_at'  => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (AiWallet $wallet) {
            $wallet->id ??= (string) Str::ulid();
        });
    }

    public function mitra(): BelongsTo
    {
        return $this->belongsTo(Mitra::class);
    }

    public function usages(): HasMany
    {
        return $this->hasMany(AiUsageRecord::class, 'wallet_id');
    }
}
