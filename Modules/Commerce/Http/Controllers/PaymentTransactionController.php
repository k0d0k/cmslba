<?php

namespace Modules\Commerce\Http\Controllers;

use App\Http\Controllers\Admin\BaseCrudController;
use Modules\Commerce\Models\PaymentTransaction;

/**
 * CRUD Transaksi Pembayaran (Payment Transactions) pada database mitra.
 *
 * Route didaftarkan oleh CommerceServiceProvider (Routes/web.php).
 */
class PaymentTransactionController extends BaseCrudController
{
    protected string $model = PaymentTransaction::class;
    protected string $view = 'admin.crud';
    protected string $routeName = 'admin.payment_transactions';
    protected string $menuKey = 'payment_transactions';
    protected bool $hasCabang = false;

    protected function title(): string
    {
        return 'Transaksi Pembayaran';
    }

    protected function fields(): array
    {
        return [
            ['name' => 'order_id', 'type' => 'relation', 'label' => 'Pesanan', 'nullable' => false,
                'relation' => ['model' => \Modules\Commerce\Models\Order::class, 'label' => 'order_number']],
            ['name' => 'invoice_id', 'type' => 'relation', 'label' => 'Invoice', 'nullable' => true,
                'relation' => ['model' => \Modules\Commerce\Models\Invoice::class, 'label' => 'number']],
            ['name' => 'provider', 'type' => 'string', 'label' => 'Penyedia', 'nullable' => false],
            ['name' => 'provider_reference', 'type' => 'string', 'label' => 'Referensi Penyedia', 'nullable' => true],
            ['name' => 'method', 'type' => 'string', 'label' => 'Metode', 'nullable' => true],
            ['name' => 'currency', 'type' => 'select', 'label' => 'Mata Uang', 'nullable' => false, 'default' => 'IDR',
                'options' => ['IDR' => 'IDR', 'USD' => 'USD']],
            ['name' => 'amount', 'type' => 'decimal', 'label' => 'Jumlah', 'nullable' => false],
            ['name' => 'status', 'type' => 'select', 'label' => 'Status', 'nullable' => false, 'default' => 'initiated',
                'options' => [
                    'initiated' => 'Dimulai',
                    'pending'   => 'Pending',
                    'settled'   => 'Selesai',
                    'failed'    => 'Gagal',
                    'refunded'  => 'Dikembalikan',
                ]],
            ['name' => 'idempotency_key', 'type' => 'string', 'label' => 'Kunci Idempotensi', 'nullable' => false],
            ['name' => 'paid_at', 'type' => 'datetime', 'label' => 'Dibayar Pada', 'nullable' => true],
        ];
    }
}
