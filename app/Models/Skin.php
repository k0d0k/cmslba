<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Skin extends Model
{
    protected $fillable = ['code', 'label', 'primary', 'sidebar', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];
}
