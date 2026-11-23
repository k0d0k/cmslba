<?php

namespace Modules\Commerce\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Harga produk per mata uang pada database mitra (tenant).
 * Skema mengikuti DDL: id ULID char(26); product_id char(26).
 */
class ProductPrice extends Model
{
    use HasUlids;

    protected $connection = 'tenant';

    protected $table = 'product_prices';

    protected $fillable = [
        'product_id', 'currency', 'amount',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
