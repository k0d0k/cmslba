<?php

namespace Modules\Mitra\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Modules\Subscription\Models\Subscription;

class MitraSubscription extends Model
{
    protected $connection = 'central';

    protected $table = 'mitra_subscriptions';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'reference',
        'mitra_id',
        'subscriptions_id',
        'status',
        'seats',
        'starts_at',
        'trial_ends_at',
        'current_period_start',
        'current_period_end',
        'cancelled_at',
        'ended_at',
        'metadata',
    ];

    protected $casts = [
        'seats' => 'integer',
        'starts_at' => 'datetime',
        'trial_ends_at' => 'datetime',
        'current_period_start' => 'datetime',
        'current_period_end' => 'datetime',
        'cancelled_at' => 'datetime',
        'ended_at' => 'datetime',
        'metadata' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function (MitraSubscription $subscription) {
            $subscription->id ??= (string) Str::ulid();
            $subscription->reference ??= 'sub_' . Str::lower(Str::random(24));
        });
    }

    public function mitra(): BelongsTo
    {
        return $this->belongsTo(Mitra::class);
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class, 'subscriptions_id', 'id');
    }
}
