<?php

namespace Modules\CrudGenerator\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Menu\Models\Menu;

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
