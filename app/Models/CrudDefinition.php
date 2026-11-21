<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CrudDefinition extends Model
{
    protected $fillable = [
        'model_name',
        'table_name',
        'connection',
        'menu_id',
        'icon',
        'fields',
        'roles',
        'created_by',
    ];

    protected $casts = [
        'fields' => 'array',
        'roles'  => 'array',
    ];

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }
}
