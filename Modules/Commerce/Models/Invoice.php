<?php

namespace Modules\Commerce\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Invoice pada database mitra (tenant).
 * Skema mengikuti DDL: id ULID char(26); order_id char(26).
 */
class Invoice extends Model
{
    use HasUlids;

    protected $connection = 'tenant';

    protected $table = 'invoices';

    protected $fillable = [
        'order_id', 'number', 'currency', 'amount', 'status', 'issued_at',
        'due_at', 'paid_at', 'metadata',
    ];

    protected $casts = [
        'amount'    => 'decimal:2',
        'issued_at' => 'datetime',
        'due_at'    => 'datetime',
        'paid_at'   => 'datetime',
        'metadata'  => 'array',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class, 'order_id');
    }
}
