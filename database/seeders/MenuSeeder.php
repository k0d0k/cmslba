<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $menus = [
            [
                'key' => 'dashboard', 'icon' => 'layout-dashboard', 'sort' => 1,
                'title' => ['id' => 'Dashboard', 'en' => 'Dashboard', 'ms' => 'Papan Pemuka'],
                'route_name' => 'admin.dashboard',
            ],
            [
                'key' => 'master', 'icon' => 'database', 'sort' => 2, 'type' => 'group',
                'title' => ['id' => 'Data Master', 'en' => 'Master Data', 'ms' => 'Data Induk'],
            ],
            [
                'key' => 'pelanggan', 'icon' => 'users', 'sort' => 1, 'parent' => 'master',
                'title' => ['id' => 'Pelanggan', 'en' => 'Customers', 'ms' => 'Pelanggan'],
                'route_name' => 'admin.pelanggan.index', 'resource' => 'Pelanggan', 'is_crud' => true,
            ],
            [
                'key' => 'cabang', 'icon' => 'git-branch', 'sort' => 2, 'parent' => 'master',
                'title' => ['id' => 'Cabang', 'en' => 'Branches', 'ms' => 'Cawangan'],
                'route_name' => 'admin.cabang.index', 'resource' => 'Cabang', 'is_crud' => true,
            ],
            [
                'key' => 'pengaturan', 'icon' => 'settings', 'sort' => 9, 'type' => 'group',
                'title' => ['id' => 'Pengaturan', 'en' => 'Settings', 'ms' => 'Tetapan'],
            ],
            [
                'key' => 'menus', 'icon' => 'list-tree', 'sort' => 1, 'parent' => 'pengaturan',
                'title' => ['id' => 'Menu Dinamis', 'en' => 'Dynamic Menu', 'ms' => 'Menu Dinamik'],
                'route_name' => 'admin.menus.index',
            ],
            [
                'key' => 'roles', 'icon' => 'shield', 'sort' => 2, 'parent' => 'pengaturan',
                'title' => ['id' => 'Role & Akses', 'en' => 'Roles & Access', 'ms' => 'Peranan & Akses'],
                'route_name' => 'admin.roles.index',
            ],
            [
                'key' => 'mitras', 'icon' => 'building-2', 'sort' => 3, 'parent' => 'pengaturan',
                'title' => ['id' => 'Multi Mitra', 'en' => 'Multi Tenant', 'ms' => 'Pelbagai Mitra'],
                'route_name' => 'admin.mitras.index',
            ],
            [
                'key' => 'generator', 'icon' => 'wand-sparkles', 'sort' => 4, 'parent' => 'pengaturan',
                'title' => ['id' => 'CRUD Generator', 'en' => 'CRUD Generator', 'ms' => 'Penjana CRUD'],
                'route_name' => 'admin.generator.index',
            ],
            [
                'key' => 'users', 'icon' => 'user-cog', 'sort' => 5, 'parent' => 'pengaturan',
                'title' => ['id' => 'User', 'en' => 'Users', 'ms' => 'Pengguna'],
                'route_name' => 'admin.users.index', 'resource' => 'User', 'is_crud' => true,
            ],
        ];

        $created = [];
        foreach ($menus as $data) {
            $parentId = isset($data['parent']) && isset($created[$data['parent']])
                ? $created[$data['parent']]->id
                : null;

            $menu = Menu::updateOrCreate(['key' => $data['key']], [
                'parent_id'  => $parentId,
                'title'      => $data['title'],
                'icon'       => $data['icon'] ?? 'circle',
                'type'       => $data['type'] ?? 'link',
                'route_name' => $data['route_name'] ?? null,
                'sort'       => $data['sort'] ?? 0,
                'is_active'  => true,
                'is_visible' => true,
                'is_crud'    => $data['is_crud'] ?? false,
                'resource'   => $data['resource'] ?? null,
            ]);

            $created[$data['key']] = $menu;
        }
    }
}
