<?php

namespace Modules\Commerce\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Transaksi pembayaran pada database mitra (tenant).
 * Skema mengikuti DDL: id ULID char(26); order_id & invoice_id char(26).
 */
class PaymentTransaction extends Model
{
    use HasUlids;

    protected $connection = 'tenant';

    protected $table = 'payment_transactions';

    protected $fillable = [
        'order_id', 'invoice_id', 'provider', 'provider_reference', 'method',
        'currency', 'amount', 'status', 'idempotency_key', 'request_payload',
        'response_payload', 'paid_at', 'metadata',
    ];

    protected $casts = [
        'amount'           => 'decimal:2',
        'request_payload'  => 'array',
        'response_payload' => 'array',
        'paid_at'          => 'datetime',
        'metadata'         => 'array',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }
}
