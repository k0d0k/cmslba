<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Subscription extends Model
{
    protected $connection = 'central';

    protected $table = 'subscription';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'code',
        'name',
        'description',
        'status',
        'price_amount',
        'currency',
        'billing_interval',
        'trial_days',
        'seat_limit',
        'sina_credit_per_patient_per_period',
        'module_keys',
    ];

    protected $casts = [
        'price_amount' => 'decimal:2',
        'trial_days'   => 'integer',
        'seat_limit'   => 'integer',
        'sina_credit_per_patient_per_period' => 'integer',
        'module_keys' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function (Subscription $subscription) {
            if (blank($subscription->id)) {
                $subscription->id = (string) Str::ulid();
            }
        });
    }
}
