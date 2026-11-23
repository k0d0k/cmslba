<?php

namespace Modules\Commerce\Http\Controllers;

use App\Http\Controllers\Admin\BaseCrudController;
use Modules\Commerce\Models\Invoice;

/**
 * CRUD Invoice (Invoices) pada database mitra.
 *
 * Route didaftarkan oleh CommerceServiceProvider (Routes/web.php).
 */
class InvoiceController extends BaseCrudController
{
    protected string $model = Invoice::class;
    protected string $view = 'admin.crud';
    protected string $routeName = 'admin.invoices';
    protected string $menuKey = 'invoices';
    protected bool $hasCabang = false;

    protected function title(): string
    {
        return 'Invoice';
    }

    protected function fields(): array
    {
        return [
            ['name' => 'order_id', 'type' => 'relation', 'label' => 'Pesanan', 'nullable' => false,
                'relation' => ['model' => \Modules\Commerce\Models\Order::class, 'label' => 'order_number']],
            ['name' => 'number', 'type' => 'string', 'label' => 'Nomor', 'nullable' => false],
            ['name' => 'currency', 'type' => 'select', 'label' => 'Mata Uang', 'nullable' => false, 'default' => 'IDR',
                'options' => ['IDR' => 'IDR', 'USD' => 'USD']],
            ['name' => 'amount', 'type' => 'decimal', 'label' => 'Jumlah', 'nullable' => false],
            ['name' => 'status', 'type' => 'select', 'label' => 'Status', 'nullable' => false, 'default' => 'issued',
                'options' => ['issued' => 'Diterbitkan', 'paid' => 'Dibayar', 'void' => 'Dibatalkan']],
            ['name' => 'issued_at', 'type' => 'datetime', 'label' => 'Diterbitkan Pada', 'nullable' => true],
            ['name' => 'due_at', 'type' => 'datetime', 'label' => 'Jatuh Tempo', 'nullable' => true],
            ['name' => 'paid_at', 'type' => 'datetime', 'label' => 'Dibayar Pada', 'nullable' => true],
        ];
    }
}
