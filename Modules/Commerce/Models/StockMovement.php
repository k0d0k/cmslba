<?php

namespace Modules\Commerce\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Pergerakan stok pada database mitra (tenant).
 * Skema mengikuti DDL: id ULID char(26); product_id & branch_id char(26).
 */
class StockMovement extends Model
{
    use HasUlids;

    protected $connection = 'tenant';

    protected $table = 'stock_movements';

    protected $fillable = [
        'product_id', 'branch_id', 'type', 'quantity', 'balance_after',
        'reason', 'reference_type', 'reference_id', 'actor_id', 'recorded_at',
    ];

    protected $casts = [
        'recorded_at' => 'datetime',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(\Modules\Cabang\Models\Cabang::class, 'branch_id');
    }
}
