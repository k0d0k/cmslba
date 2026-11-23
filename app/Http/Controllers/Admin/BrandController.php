<?php

namespace App\Http\Controllers\Admin;

class BrandController extends BaseCrudController
{
    protected string $model = \App\Models\Brand::class;
    protected string $view = 'admin.crud';
    protected string $routeName = 'admin.brand';
    protected string $menuKey = 'brand';
    protected bool $hasCabang = false;

    protected function title(): string
    {
        return __('common.brand');
    }

    protected function fields(): array
    {
        return [
            ['name' => 'sku', 'type' => 'string', 'label' => __('common.sku'), 'nullable' => false],
            ['name' => 'name', 'type' => 'string', 'label' => __('common.brand'), 'nullable' => false],
            ['name' => 'image', 'type' => 'image', 'label' => __('common.image'), 'nullable' => true],
            ['name' => 'is_active', 'type' => 'boolean', 'label' => __('common.active'), 'nullable' => false],
        ];
    }
}