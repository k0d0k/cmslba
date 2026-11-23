<?php

namespace Modules\Commerce\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Stok inventaris per cabang pada database mitra (tenant).
 * Skema mengikuti DDL: id ULID char(26); product_id & branch_id char(26).
 */
class InventoryStock extends Model
{
    use HasUlids;

    protected $connection = 'tenant';

    protected $table = 'inventory_stocks';

    protected $fillable = [
        'product_id', 'branch_id', 'quantity', 'reorder_level',
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
