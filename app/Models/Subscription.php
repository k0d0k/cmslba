<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasSlug;

    protected $connection = 'central';

    protected $table = 'subscriptions';

    protected $fillable = ['slug', 'name', 'code', 'price', 'duration_days', 'description', 'is_active'];

    protected $casts = [
        'price'         => 'decimal:2',
        'duration_days' => 'integer',
        'is_active'     => 'boolean',
    ];

    public $slug_source = 'name';
}
