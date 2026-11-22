<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasSlug;

    protected $connection = 'central';

    protected $table = 'brands';

    protected $fillable = ['slug', 'sku', 'name', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public $slug_source = 'name';
}