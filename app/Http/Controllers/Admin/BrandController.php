<?php

namespace App\Http\Controllers\Admin;

class BrandController extends BaseCrudController
{
    protected string $model = \App\Models\Brand::class;
    protected string $view = 'admin.crud';
    protected string $routeName = 'admin.brand';
    protected string $title = 'Brand';
    protected string $menuKey = 'brand';
    protected bool $hasCabang = false;
    protected array $fields = [
        ['name' => 'sku', 'type' => 'string', 'label' => 'SKU', 'nullable' => false],
        ['name' => 'name', 'type' => 'string', 'label' => 'Brand', 'nullable' => false],
        ['name' => 'image', 'type' => 'image', 'label' => 'Gambar', 'nullable' => true],
        ['name' => 'is_active', 'type' => 'boolean', 'label' => 'Aktif', 'nullable' => false],
    ];
}