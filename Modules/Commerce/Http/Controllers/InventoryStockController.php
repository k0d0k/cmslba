<?php

namespace Modules\Commerce\Http\Controllers;

use App\Http\Controllers\Admin\BaseCrudController;
use Modules\Commerce\Models\InventoryStock;

/**
 * CRUD Stok Inventaris (Inventory Stocks) pada database mitra.
 *
 * Route didaftarkan oleh CommerceServiceProvider (Routes/web.php).
 */
class InventoryStockController extends BaseCrudController
{
    protected string $model = InventoryStock::class;
    protected string $view = 'admin.crud';
    protected string $routeName = 'admin.inventory_stocks';
    protected string $menuKey = 'inventory_stocks';
    protected bool $hasCabang = false;

    protected function title(): string
    {
        return 'Stok Inventaris';
    }

    protected function fields(): array
    {
        return [
            ['name' => 'product_id', 'type' => 'relation', 'label' => 'Produk', 'nullable' => false,
                'relation' => ['model' => \Modules\Commerce\Models\Product::class, 'label' => 'name']],
            ['name' => 'branch_id', 'type' => 'relation', 'label' => 'Cabang', 'nullable' => false,
                'relation' => ['model' => \Modules\Cabang\Models\Cabang::class, 'label' => 'name']],
            ['name' => 'quantity', 'type' => 'integer', 'label' => 'Jumlah', 'nullable' => false],
            ['name' => 'reorder_level', 'type' => 'integer', 'label' => 'Batas Pemesanan Ulang', 'nullable' => true],
        ];
    }
}
