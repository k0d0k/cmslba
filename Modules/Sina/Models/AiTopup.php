<?php

namespace Modules\Sina\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Modules\Mitra\Models\Mitra;
use Modules\User\Models\User;

/**
 * Riwayat top-up kredit SINA AI (database pusat).
 */
class AiTopup extends Model
{
    protected $connection = 'central';

    protected $table = 'ai_topups';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'mitra_id',
        'wallet_id',
        'credits',
        'price_per_credit',
        'total_amount',
        'note',
        'created_by',
    ];

    protected $casts = [
        'credits'          => 'integer',
        'price_per_credit' => 'decimal:2',
        'total_amount'     => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function (AiTopup $topup) {
            $topup->id ??= (string) Str::ulid();
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

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
