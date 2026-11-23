<?php

namespace Modules\Commerce\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Pesanan (order) pada database mitra (tenant).
 * Skema mengikuti DDL: id ULID char(26); branch_id & patient_id char(26).
 */
class Order extends Model
{
    use HasUlids;

    protected $connection = 'tenant';

    protected $table = 'orders';

    protected $fillable = [
        'order_number', 'source', 'branch_id', 'patient_id', 'customer_name',
        'customer_email', 'customer_phone', 'currency', 'subtotal',
        'discount_total', 'tax_total', 'grand_total', 'paid_total', 'status',
        'fulfillment_status', 'placed_by', 'placed_at', 'notes', 'metadata',
    ];

    protected $casts = [
        'subtotal'       => 'decimal:2',
        'discount_total' => 'decimal:2',
        'tax_total'      => 'decimal:2',
        'grand_total'    => 'decimal:2',
        'paid_total'     => 'decimal:2',
        'placed_at'      => 'datetime',
        'metadata'       => 'array',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(\Modules\Cabang\Models\Cabang::class, 'branch_id');
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(\Modules\Pelanggan\Models\Pelanggan::class, 'patient_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'order_id');
    }
}
