<?php

namespace App\Http\Controllers\Admin;

/**
 * Contoh modul CRUD Pelanggan (data multi cabang pada database mitra).
 */
class PelangganController extends BaseCrudController
{
    protected string $model = \App\Models\Tenant\Pelanggan::class;
    protected string $view = 'admin.crud';
    protected string $routeName = 'admin.pelanggan';
    protected string $title = 'Pelanggan';
    protected string $menuKey = 'pelanggan';
    protected bool $hasCabang = true;
    protected array $fields = [
        ['name' => 'name', 'type' => 'string', 'label' => 'Nama', 'nullable' => false],
        ['name' => 'email', 'type' => 'email', 'label' => 'Email', 'nullable' => true],
        ['name' => 'phone', 'type' => 'string', 'label' => 'Telepon', 'nullable' => true],
        ['name' => 'cabang_id', 'type' => 'relation', 'label' => 'Cabang', 'nullable' => true, 'relation' => ['model' => \App\Models\Tenant\Cabang::class, 'label' => 'name']],
        ['name' => 'birth_date', 'type' => 'date', 'label' => 'Tanggal Lahir', 'nullable' => true],
        ['name' => 'address', 'type' => 'text', 'label' => 'Alamat', 'nullable' => true],
        ['name' => 'is_active', 'type' => 'boolean', 'label' => 'Aktif', 'nullable' => false],
    ];
}
