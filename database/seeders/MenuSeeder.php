<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Susunan menu admin:
     *   Dashboard
     *   Data Mitra  -> Multi Mitra, Cabang, Pelanggan
     *   Data Master -> Brand, Registry Modul, Paket Langganan, User, Integrasi SINA AI
     *   Pengaturan  -> Setting, Menu Dinamis, Role & Akses, CRUD Generator
     *
     * Urutan array sengaja disusun agar parent selalu dibuat sebelum anak.
     */
    public function run(): void
    {
        $menus = [
            // ── Dashboard ───────────────────────────────────────────────
            [
                'key' => 'dashboard', 'icon' => 'layout-dashboard', 'sort' => 0,
                'title' => ['id' => 'Dashboard', 'en' => 'Dashboard', 'ms' => 'Papan Pemuka'],
                'route_name' => 'admin.dashboard',
            ],

            // ── Data Mitra (group) ──────────────────────────────────────
            [
                'key' => 'mitra', 'icon' => 'user-group', 'sort' => 1, 'type' => 'group',
                'title' => ['id' => 'Data Mitra', 'en' => 'Partners Data', 'ms' => 'Data Rakan'],
            ],
            [
                'key' => 'mitras', 'icon' => 'building-2', 'sort' => 0, 'parent' => 'mitra',
                'title' => ['id' => 'Multi Mitra', 'en' => 'Multi Partners', 'ms' => 'Pelbagai Mitra'],
                'route_name' => 'admin.mitras.index',
            ],
            [
                'key' => 'cabang', 'icon' => 'git-branch', 'sort' => 1, 'parent' => 'mitra',
                'title' => ['id' => 'Cabang', 'en' => 'Branches', 'ms' => 'Cawangan'],
                'route_name' => 'admin.cabang.index', 'resource' => 'Cabang', 'is_crud' => true,
            ],
            [
                'key' => 'pelanggan', 'icon' => 'users', 'sort' => 2, 'parent' => 'mitra',
                'title' => ['id' => 'Pelanggan', 'en' => 'Customers', 'ms' => 'Pelanggan'],
                'route_name' => 'admin.pelanggan.index', 'resource' => 'Pelanggan', 'is_crud' => true,
            ],
            [
                'key' => 'mitra_website', 'icon' => 'globe', 'sort' => 3, 'parent' => 'mitra',
                'title' => ['id' => 'Website Mitra', 'en' => 'Partner Website', 'ms' => 'Laman Rakan'],
                'route_name' => 'admin.mitra_website.index',
            ],

            // ── Data Master (group) ─────────────────────────────────────
            [
                'key' => 'master', 'icon' => 'database', 'sort' => 2, 'type' => 'group',
                'title' => ['id' => 'Data Master', 'en' => 'Master Data', 'ms' => 'Data Induk'],
            ],
            [
                'key' => 'brand', 'icon' => 'shopping-cart', 'sort' => 0, 'parent' => 'master',
                'title' => ['id' => 'Brand', 'en' => 'Brand', 'ms' => 'Brand'],
                'route_name' => 'admin.brand.index', 'resource' => 'Brand', 'is_crud' => true,
            ],
            [
                'key' => 'modules', 'icon' => 'puzzle', 'sort' => 1, 'parent' => 'master',
                'title' => ['id' => 'Registry Modul', 'en' => 'Module Registry', 'ms' => 'Registry Modul'],
                'route_name' => 'admin.modules.index', 'resource' => 'Module', 'is_crud' => true,
            ],
            [
                'key' => 'subscriptions', 'icon' => 'credit-card', 'sort' => 2, 'parent' => 'master',
                'title' => ['id' => 'Paket Langganan', 'en' => 'Subscription Plans', 'ms' => 'Pakej Langganan'],
                'route_name' => 'admin.subscriptions.index', 'resource' => 'Subscription', 'is_crud' => true,
            ],
            [
                'key' => 'users', 'icon' => 'user-cog', 'sort' => 3, 'parent' => 'master',
                'title' => ['id' => 'User', 'en' => 'Users', 'ms' => 'Pengguna'],
                'route_name' => 'admin.users.index', 'resource' => 'User', 'is_crud' => true,
            ],
            [
                'key' => 'sina_ai', 'icon' => 'brain-circuit', 'sort' => 4, 'parent' => 'master',
                'title' => ['id' => 'Integrasi SINA AI', 'en' => 'SINA AI Integration', 'ms' => 'Integrasi SINA AI'],
                'route_name' => 'admin.sina.index',
            ],
            [
                'key' => 'payment_providers', 'icon' => 'wallet', 'sort' => 5, 'parent' => 'master',
                'title' => ['id' => 'Metode Pembayaran', 'en' => 'Payment Methods', 'ms' => 'Kaedah Pembayaran'],
                'route_name' => 'admin.payment_providers.index', 'resource' => 'PaymentProvider', 'is_crud' => true,
            ],
            [
                'key' => 'website', 'icon' => 'globe', 'sort' => 6, 'parent' => 'master',
                'title' => ['id' => 'Website / Frontpage', 'en' => 'Website / Frontpage', 'ms' => 'Laman / Frontpage'],
                'route_name' => 'admin.website.index',
            ],

            // ── Pengaturan (group) ──────────────────────────────────────
            [
                'key' => 'pengaturan', 'icon' => 'settings', 'sort' => 3, 'type' => 'group',
                'title' => ['id' => 'Pengaturan', 'en' => 'Settings', 'ms' => 'Tetapan'],
            ],
            [
                'key' => 'setting_aplikasi', 'icon' => 'app-window', 'sort' => 0, 'parent' => 'pengaturan',
                'title' => ['id' => 'Setting', 'en' => 'Settings', 'ms' => 'Tetapan'],
                'route_name' => 'admin.setting-app.index',
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
                'key' => 'generator', 'icon' => 'wand-sparkles', 'sort' => 3, 'parent' => 'pengaturan',
                'title' => ['id' => 'CRUD Generator', 'en' => 'CRUD Generator', 'ms' => 'Penjana CRUD'],
                'route_name' => 'admin.generator.index',
            ],
            [
                'key' => 'audit_trail', 'icon' => 'history', 'sort' => 4, 'parent' => 'pengaturan',
                'title' => ['id' => 'Jejak Audit', 'en' => 'Audit Trail', 'ms' => 'Jejak Audit'],
                'route_name' => 'admin.audit.index',
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
