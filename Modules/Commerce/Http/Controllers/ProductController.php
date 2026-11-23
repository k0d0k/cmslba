<?php

namespace Modules\Commerce\Http\Controllers;

use App\Http\Controllers\Admin\BaseCrudController;
use Modules\Commerce\Models\Product;

/**
 * CRUD Produk (Products) pada database mitra.
 *
 * Route didaftarkan oleh CommerceServiceProvider (Routes/web.php).
 */
class ProductController extends BaseCrudController
{
    protected string $model = Product::class;
    protected string $view = 'admin.crud';
    protected string $routeName = 'admin.products';
    protected string $menuKey = 'products';
    protected bool $hasCabang = false;

    protected function title(): string
    {
        return 'Produk';
    }

    protected function fields(): array
    {
        return [
            ['name' => 'category_id', 'type' => 'relation', 'label' => 'Kategori', 'nullable' => true,
                'relation' => ['model' => \Modules\Commerce\Models\ProductCategory::class, 'label' => 'name']],
            ['name' => 'sku', 'type' => 'string', 'label' => 'SKU', 'nullable' => false],
            ['name' => 'slug', 'type' => 'string', 'label' => 'Slug', 'nullable' => true],
            ['name' => 'name', 'type' => 'string', 'label' => 'Nama', 'nullable' => false],
            ['name' => 'description', 'type' => 'text', 'label' => 'Deskripsi', 'nullable' => true],
            ['name' => 'unit', 'type' => 'string', 'label' => 'Satuan', 'nullable' => false, 'default' => 'pcs'],
            ['name' => 'kind', 'type' => 'select', 'label' => 'Jenis', 'nullable' => false, 'default' => 'product',
                'options' => ['product' => 'Produk', 'service' => 'Layanan']],
            ['name' => 'base_currency', 'type' => 'select', 'label' => 'Mata Uang', 'nullable' => false, 'default' => 'IDR',
                'options' => ['IDR' => 'IDR', 'USD' => 'USD']],
            ['name' => 'base_price', 'type' => 'decimal', 'label' => 'Harga Dasar', 'nullable' => false],
            ['name' => 'track_inventory', 'type' => 'boolean', 'label' => 'Lacak Inventaris', 'nullable' => false],
            ['name' => 'status', 'type' => 'select', 'label' => 'Status', 'nullable' => false, 'default' => 'active',
                'options' => ['active' => 'Aktif', 'inactive' => 'Nonaktif']],
        ];
    }
}
