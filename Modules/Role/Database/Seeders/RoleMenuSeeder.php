<?php

namespace Modules\Role\Database\Seeders;

use Modules\Menu\Models\Menu;
use Modules\Menu\Services\MenuService;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleMenuSeeder extends Seeder
{
    /** key menu => abilities per role */
    protected array $map = [
        'admin_principal' => '*',
        'user_principal' => [
            'dashboard' => ['view'],
            'pelanggan' => ['view', 'export'],
            'cabang'    => ['view'],
            'users'     => ['view'],
            'brand'     => ['view'],
            'modules'   => ['view'],
            'subscriptions' => ['view'],
            'sina_ai'   => ['view'],
            'payment_providers' => ['view'],
            'website'   => ['view'],
            'setting_aplikasi' => ['view'],
            'audit_trail' => ['view'],
        ],
        'pemilik_mitra' => [
            'dashboard' => ['view'],
            'pelanggan' => ['view', 'create', 'update', 'delete', 'export'],
            'appointments' => ['view', 'create', 'update', 'delete', 'export'],
            'practitioners' => ['view', 'create', 'update', 'delete', 'export'],
            'consultations_group' => ['view'],
            'consultations' => ['view', 'create', 'update', 'delete', 'export'],
            'commerce_group' => ['view'],
            'products' => ['view', 'create', 'update', 'delete', 'export'],
            'product_categories' => ['view', 'create', 'update', 'delete', 'export'],
            'orders' => ['view', 'create', 'update', 'delete', 'export'],
            'invoices' => ['view', 'create', 'update', 'delete', 'export'],
            'payment_transactions' => ['view', 'create', 'update', 'delete', 'export'],
            'inventory_stocks' => ['view', 'create', 'update', 'delete', 'export'],
            'lba_group' => ['view'],
            'lba_examinations' => ['view', 'create', 'update', 'delete', 'export'],
            'lba_assessments' => ['view', 'create', 'update', 'delete', 'export'],
            'lba_images' => ['view', 'create', 'update', 'delete', 'export'],
            'lba_report_versions' => ['view', 'create', 'update', 'delete', 'export'],
            'lba_reviews' => ['view', 'create', 'update', 'delete', 'export'],
            'cabang'    => ['view', 'create', 'update', 'delete'],
            'mitras'    => ['view'],
            'brand'     => ['view'],
            'users'     => ['view', 'create', 'update', 'delete'],
            'sina_ai'   => ['view', 'update'],
            'payment_providers' => ['view'],
            'mitra_website' => ['view', 'create', 'update'],
            'audit_trail' => ['view'],
        ],
        'admin_mitra' => [
            'dashboard' => ['view'],
            'pelanggan' => ['view', 'create', 'update', 'delete', 'export'],
            'appointments' => ['view', 'create', 'update', 'delete', 'export'],
            'practitioners' => ['view', 'create', 'update', 'delete', 'export'],
            'consultations_group' => ['view'],
            'consultations' => ['view', 'create', 'update', 'delete', 'export'],
            'commerce_group' => ['view'],
            'products' => ['view', 'create', 'update', 'delete', 'export'],
            'product_categories' => ['view', 'create', 'update', 'delete', 'export'],
            'orders' => ['view', 'create', 'update', 'delete', 'export'],
            'invoices' => ['view', 'create', 'update', 'delete', 'export'],
            'payment_transactions' => ['view', 'create', 'update', 'delete', 'export'],
            'inventory_stocks' => ['view', 'create', 'update', 'delete', 'export'],
            'lba_group' => ['view'],
            'lba_examinations' => ['view', 'create', 'update', 'delete', 'export'],
            'lba_assessments' => ['view', 'create', 'update', 'delete', 'export'],
            'lba_images' => ['view', 'create', 'update', 'delete', 'export'],
            'lba_report_versions' => ['view', 'create', 'update', 'delete', 'export'],
            'lba_reviews' => ['view', 'create', 'update', 'delete', 'export'],
            'cabang'    => ['view', 'create', 'update', 'delete'],
            'mitras'    => ['view'],
            'brand'     => ['view'],
            'users'     => ['view', 'create', 'update', 'delete'],
            'sina_ai'   => ['view', 'update'],
            'payment_providers' => ['view'],
            'mitra_website' => ['view', 'create', 'update'],
            'audit_trail' => ['view'],
        ],
        'user_mitra' => [
            'dashboard' => ['view'],
            'pelanggan' => ['view'],
        ],
        'staff_admin' => [
            'dashboard' => ['view'],
            'pelanggan' => ['view', 'create', 'update'],
            'cabang'    => ['view'],
            'brand'     => ['view'],
        ],
        'operator' => [
            'dashboard' => ['view'],
            'pelanggan' => ['view', 'create'],
        ],
        'keuangan_penjualan' => [
            'dashboard' => ['view'],
            'pelanggan' => ['view', 'export'],
            'cabang'    => ['view'],
        ],
    ];

    public function run(): void
    {
        $service = app(MenuService::class);

        foreach ($this->map as $roleName => $menus) {
            $role = Role::where('name', $roleName)->first();
            if (! $role) {
                continue;
            }

            if ($menus === '*') {
                foreach (Menu::all() as $menu) {
                    $service->assign($role, $menu->id, MenuService::ABILITIES);
                }
                continue;
            }

            foreach ($menus as $key => $abilities) {
                $menu = Menu::where('key', $key)->first();
                if ($menu) {
                    $service->assign($role, $menu->id, $abilities);
                }
            }
        }
    }
}
