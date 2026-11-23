<?php

namespace Modules\Commerce\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Baris item pesanan pada database mitra (tenant).
 * Skema mengikuti DDL: id ULID char(26); order_id & product_id char(26).
 */
class OrderItem extends Model
{
    use HasUlids;

    protected $connection = 'tenant';

    protected $table = 'order_items';

    protected $fillable = [
        'order_id', 'product_id', 'name', 'sku', 'unit_price', 'quantity',
        'line_total', 'currency', 'metadata',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'line_total' => 'decimal:2',
        'metadata'   => 'array',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
