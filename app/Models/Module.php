<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasSlug;

    protected $connection = 'central';

    protected $table = 'modules';

    protected $fillable = ['slug', 'name', 'code', 'version', 'description', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public $slug_source = 'name';
}
