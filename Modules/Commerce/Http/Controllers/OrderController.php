<?php

namespace Modules\Commerce\Http\Controllers;

use App\Http\Controllers\Admin\BaseCrudController;
use Modules\Commerce\Models\Order;

/**
 * CRUD Pesanan (Orders) pada database mitra.
 *
 * Route didaftarkan oleh CommerceServiceProvider (Routes/web.php).
 */
class OrderController extends BaseCrudController
{
    protected string $model = Order::class;
    protected string $view = 'admin.crud';
    protected string $routeName = 'admin.orders';
    protected string $menuKey = 'orders';
    protected bool $hasCabang = false;

    protected function title(): string
    {
        return 'Pesanan';
    }

    protected function fields(): array
    {
        return [
            ['name' => 'order_number', 'type' => 'string', 'label' => 'Nomor Pesanan', 'nullable' => false],
            ['name' => 'source', 'type' => 'select', 'label' => 'Sumber', 'nullable' => false, 'default' => 'pos',
                'options' => ['pos' => 'POS', 'online' => 'Online', 'manual' => 'Manual']],
            ['name' => 'branch_id', 'type' => 'relation', 'label' => 'Cabang', 'nullable' => false,
                'relation' => ['model' => \Modules\Cabang\Models\Cabang::class, 'label' => 'name']],
            ['name' => 'patient_id', 'type' => 'relation', 'label' => 'Pelanggan', 'nullable' => true,
                'relation' => ['model' => \Modules\Pelanggan\Models\Pelanggan::class, 'label' => 'name']],
            ['name' => 'customer_name', 'type' => 'string', 'label' => 'Nama Pelanggan', 'nullable' => true],
            ['name' => 'customer_email', 'type' => 'string', 'label' => 'Email Pelanggan', 'nullable' => true],
            ['name' => 'customer_phone', 'type' => 'string', 'label' => 'Telepon Pelanggan', 'nullable' => true],
            ['name' => 'currency', 'type' => 'select', 'label' => 'Mata Uang', 'nullable' => false, 'default' => 'IDR',
                'options' => ['IDR' => 'IDR', 'USD' => 'USD']],
            ['name' => 'subtotal', 'type' => 'decimal', 'label' => 'Subtotal', 'nullable' => false],
            ['name' => 'discount_total', 'type' => 'decimal', 'label' => 'Total Diskon', 'nullable' => false],
            ['name' => 'tax_total', 'type' => 'decimal', 'label' => 'Total Pajak', 'nullable' => false],
            ['name' => 'grand_total', 'type' => 'decimal', 'label' => 'Total Akhir', 'nullable' => false],
            ['name' => 'paid_total', 'type' => 'decimal', 'label' => 'Total Dibayar', 'nullable' => false],
            ['name' => 'status', 'type' => 'select', 'label' => 'Status', 'nullable' => false, 'default' => 'pending',
                'options' => ['pending' => 'Pending', 'paid' => 'Dibayar', 'partial' => 'Sebagian', 'cancelled' => 'Dibatalkan']],
            ['name' => 'fulfillment_status', 'type' => 'select', 'label' => 'Status Pemenuhan', 'nullable' => false, 'default' => 'pending',
                'options' => ['pending' => 'Pending', 'fulfilled' => 'Terpenuhi', 'cancelled' => 'Dibatalkan']],
            ['name' => 'notes', 'type' => 'text', 'label' => 'Catatan', 'nullable' => true],
        ];
    }
}
