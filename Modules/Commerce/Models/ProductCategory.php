<?php

namespace Modules\Commerce\Models;

use App\Models\Concerns\HasSlug;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Kategori produk pada database mitra (tenant).
 * Skema mengikuti DDL: id ULID char(26); parent_id char(26).
 */
class ProductCategory extends Model
{
    use HasUlids;
    use HasSlug;

    protected $connection = 'tenant';

    protected $table = 'product_categories';

    protected $fillable = [
        'parent_id', 'name', 'slug', 'description', 'position', 'status',
    ];

    public $slug_source = 'name';

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }
}
