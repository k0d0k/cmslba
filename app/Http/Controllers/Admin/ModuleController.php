<?php

namespace App\Http\Controllers\Admin;

/**
 * Registry modul aplikasi (menu dinamis). CRUD generik dari tabel `modules`.
 */
class ModuleController extends BaseCrudController
{
    protected string $model = \App\Models\Module::class;
    protected string $routeName = 'admin.modules';
    protected string $title = 'Modul';
    protected string $menuKey = 'modules';
    protected array $fields = [
        ['name' => 'name', 'type' => 'string', 'label' => 'Nama Modul', 'nullable' => false],
        ['name' => 'code', 'type' => 'string', 'label' => 'Kode', 'nullable' => false, 'unique' => true],
        ['name' => 'version', 'type' => 'string', 'label' => 'Versi', 'nullable' => true],
        ['name' => 'description', 'type' => 'text', 'label' => 'Deskripsi', 'nullable' => true],
        ['name' => 'is_active', 'type' => 'boolean', 'label' => 'Aktif', 'nullable' => true, 'default' => true],
    ];
}
