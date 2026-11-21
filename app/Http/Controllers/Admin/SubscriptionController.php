<?php

namespace App\Http\Controllers\Admin;

/**
 * Paket langganan (menu dinamis). CRUD generik dari tabel `subscription`.
 */
class SubscriptionController extends BaseCrudController
{
    protected string $model = \App\Models\Subscription::class;
    protected string $routeName = 'admin.subscriptions';
    protected string $title = 'Paket Langganan';
    protected string $menuKey = 'subscriptions';
    protected array $fields = [
        ['name' => 'code', 'type' => 'string', 'label' => 'Kode', 'nullable' => false, 'unique' => true],
        ['name' => 'name', 'type' => 'string', 'label' => 'Nama Paket', 'nullable' => false],
        ['name' => 'description', 'type' => 'text', 'label' => 'Deskripsi', 'nullable' => true],
        ['name' => 'status', 'type' => 'select', 'label' => 'Status', 'nullable' => false, 'default' => 'active',
            'options' => ['active' => 'Aktif', 'inactive' => 'Nonaktif']],
        ['name' => 'price_amount', 'type' => 'decimal', 'label' => 'Harga', 'nullable' => false, 'default' => 0],
        ['name' => 'currency', 'type' => 'string', 'label' => 'Mata Uang', 'nullable' => false, 'default' => 'IDR'],
        ['name' => 'billing_interval', 'type' => 'select', 'label' => 'Interval Tagihan', 'nullable' => false, 'default' => 'monthly',
            'options' => ['monthly' => 'Bulanan', 'yearly' => 'Tahunan', 'weekly' => 'Mingguan', 'daily' => 'Harian']],
        ['name' => 'trial_days', 'type' => 'integer', 'label' => 'Trial (hari)', 'nullable' => false, 'default' => 0],
        ['name' => 'seat_limit', 'type' => 'integer', 'label' => 'Batas Seat', 'nullable' => true],
        ['name' => 'module_keys', 'type' => 'text', 'label' => 'Module Keys (JSON)', 'nullable' => true],
    ];
}
