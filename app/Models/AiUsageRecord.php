<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * Riwayat pemakaian kredit SINA AI (database pusat).
 */
class AiUsageRecord extends Model
{
    protected $connection = 'central';

    protected $table = 'ai_usage_records';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'mitra_id',
        'wallet_id',
        'job_reference',
        'provider',
        'model_name',
        'model_version',
        'pricing_rule_ref',
        'unit_price_credits',
        'credit_units',
        'idempotency_key',
        'recorded_at',
    ];

    protected $casts = [
        'unit_price_credits' => 'integer',
        'credit_units'       => 'integer',
        'recorded_at'        => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (AiUsageRecord $record) {
            $record->id ??= (string) Str::ulid();
        });
    }

    public function mitra(): BelongsTo
    {
        return $this->belongsTo(Mitra::class);
    }

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(AiWallet::class, 'wallet_id');
    }
}
