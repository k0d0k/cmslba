<?php

namespace App\Http\Controllers\Admin;

/**
 * Contoh modul CRUD yang sudah tersedia (dibuat manual),
 * sama seperti hasil CRUD Generator.
 */
class CabangController extends BaseCrudController
{
    protected string $model = \App\Models\Tenant\Cabang::class;
    protected string $view = 'admin.crud';
    protected string $routeName = 'admin.cabang';
    protected string $title = 'Cabang';
    protected string $menuKey = 'cabang';
    protected bool $hasCabang = false;
    protected array $fields = [
        ['name' => 'name', 'type' => 'string', 'label' => 'Nama Cabang', 'nullable' => false],
        ['name' => 'code', 'type' => 'string', 'label' => 'Kode', 'nullable' => true],
        ['name' => 'phone', 'type' => 'string', 'label' => 'Telepon', 'nullable' => true],
        ['name' => 'address', 'type' => 'text', 'label' => 'Alamat', 'nullable' => true],
        ['name' => 'is_active', 'type' => 'boolean', 'label' => 'Aktif', 'nullable' => false],
    ];
}
