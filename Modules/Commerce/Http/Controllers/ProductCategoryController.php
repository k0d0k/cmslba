<?php

namespace Modules\Commerce\Http\Controllers;

use App\Http\Controllers\Admin\BaseCrudController;
use Modules\Commerce\Models\ProductCategory;

/**
 * CRUD Kategori Produk (Product Categories) pada database mitra.
 *
 * Route didaftarkan oleh CommerceServiceProvider (Routes/web.php).
 */
class ProductCategoryController extends BaseCrudController
{
    protected string $model = ProductCategory::class;
    protected string $view = 'admin.crud';
    protected string $routeName = 'admin.product_categories';
    protected string $menuKey = 'product_categories';
    protected bool $hasCabang = false;

    protected function title(): string
    {
        return 'Kategori Produk';
    }

    protected function fields(): array
    {
        return [
            ['name' => 'name', 'type' => 'string', 'label' => 'Nama', 'nullable' => false],
            ['name' => 'slug', 'type' => 'string', 'label' => 'Slug', 'nullable' => true],
            ['name' => 'description', 'type' => 'text', 'label' => 'Deskripsi', 'nullable' => true],
            ['name' => 'position', 'type' => 'integer', 'label' => 'Urutan', 'nullable' => true, 'default' => 0],
            ['name' => 'status', 'type' => 'select', 'label' => 'Status', 'nullable' => false, 'default' => 'active',
                'options' => ['active' => 'Aktif', 'inactive' => 'Nonaktif']],
        ];
    }
}
