<?php

namespace App\Http\Controllers\Admin;

/**
 * Paket langganan (menu dinamis). CRUD generik dari tabel `subscriptions`.
 */
class SubscriptionController extends BaseCrudController
{
    protected string $model = \App\Models\Subscription::class;
    protected string $routeName = 'admin.subscriptions';
    protected string $title = 'Paket Langganan';
    protected string $menuKey = 'subscriptions';
    protected array $fields = [
        ['name' => 'name', 'type' => 'string', 'label' => 'Nama Paket', 'nullable' => false],
        ['name' => 'code', 'type' => 'string', 'label' => 'Kode', 'nullable' => false, 'unique' => true],
        ['name' => 'price', 'type' => 'decimal', 'label' => 'Harga', 'nullable' => false],
        ['name' => 'duration_days', 'type' => 'integer', 'label' => 'Durasi (hari)', 'nullable' => false],
        ['name' => 'description', 'type' => 'text', 'label' => 'Deskripsi', 'nullable' => true],
        ['name' => 'is_active', 'type' => 'boolean', 'label' => 'Aktif', 'nullable' => true, 'default' => true],
    ];
}
