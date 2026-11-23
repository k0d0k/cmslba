<?php

namespace Modules\ModuleRegistry\Http\Controllers;

use App\Http\Controllers\Admin\BaseCrudController;

/**
 * Registry modul aplikasi (menu dinamis). CRUD generik dari tabel `modules`.
 */
class ModuleController extends BaseCrudController
{
    protected string $model = \Modules\ModuleRegistry\Models\Module::class;
    protected string $routeName = 'admin.modules';
    protected string $menuKey = 'modules';

    protected function title(): string
    {
        return __('common.module');
    }

    protected function fields(): array
    {
        return [
            ['name' => 'name', 'type' => 'string', 'label' => __('common.module_name'), 'nullable' => false],
            ['name' => 'code', 'type' => 'string', 'label' => __('common.code'), 'nullable' => false, 'unique' => true],
            ['name' => 'version', 'type' => 'string', 'label' => __('common.version'), 'nullable' => true],
            ['name' => 'description', 'type' => 'text', 'label' => __('common.description'), 'nullable' => true],
            ['name' => 'is_active', 'type' => 'boolean', 'label' => __('common.active'), 'nullable' => true, 'default' => true],
        ];
    }
}
