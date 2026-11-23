<?php

namespace Modules\Commerce\Models;

use App\Models\Concerns\HasSlug;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Produk pada database mitra (tenant).
 * Skema mengikuti DDL: id ULID char(26); category_id char(26).
 */
class Product extends Model
{
    use HasUlids;
    use HasSlug;

    protected $connection = 'tenant';

    protected $table = 'products';

    protected $fillable = [
        'brand_id', 'category_id', 'sku', 'slug', 'name', 'description',
        'unit', 'kind', 'base_currency', 'base_price', 'track_inventory',
        'status', 'metadata',
    ];

    protected $casts = [
        'base_price'      => 'decimal:2',
        'track_inventory' => 'boolean',
        'metadata'        => 'array',
    ];

    public $slug_source = 'name';

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function prices(): HasMany
    {
        return $this->hasMany(ProductPrice::class, 'product_id');
    }

    public function stocks(): HasMany
    {
        return $this->hasMany(InventoryStock::class, 'product_id');
    }
}
