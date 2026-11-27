<?php

namespace Modules\Menu\Database\Seeders;

use Modules\Menu\Models\Menu;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Susunan menu admin:
     *   Dashboard
     *   Data Mitra   -> Multi Mitra, Cabang, Pelanggan, Website Mitra, Agenda (events)
     *   Data Master  -> Brand, Registry Modul, Paket Langganan, User, Integrasi SINA AI
     *   Modul        -> Janji Temu (appointments)
     *   Konsultasi   -> Konsultasi (consultations)
     *   Perdagangan  -> Produk, Kategori Produk, Pesanan, Invoice, Transaksi Pembayaran, Stok Inventaris (commerce)
     *   Praktisi     -> Praktisi (practitioners)
     *   LBA          -> Pemeriksaan, Asesmen, Gambar, Laporan, Review (lba)
     *   Pengaturan   -> Setting, Menu Dinamis, Role & Akses, CRUD Generator
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
            [
                'key' => 'events', 'icon' => 'calendar-days', 'sort' => 4, 'parent' => 'mitra',
                'title' => ['id' => 'Agenda', 'en' => 'Events', 'ms' => 'Acara'],
                'route_name' => 'admin.events.index', 'resource' => 'Event',
                'module_key' => 'events', 'is_crud' => true,
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

            // ── Modules (group) ─────────────────────────────────────────
            [
                'key' => 'modules_group', 'icon' => 'puzzle', 'sort' => 3, 'type' => 'group',
                'title' => ['id' => 'Modul', 'en' => 'Modules', 'ms' => 'Modul'],
            ],
            [
                'key' => 'appointments', 'icon' => 'calendar-clock', 'sort' => 0, 'parent' => 'modules_group',
                'title' => ['id' => 'Janji Temu', 'en' => 'Appointments', 'ms' => 'Temu Janji'],
                'route_name' => 'admin.appointments.index', 'resource' => 'Appointment',
                'module_key' => 'appointments', 'is_crud' => true,
            ],

            // ── Consultations (group) ───────────────────────────────────
            [
                'key' => 'consultations_group', 'icon' => 'messages-square', 'sort' => 4, 'type' => 'group',
                'title' => ['id' => 'Konsultasi', 'en' => 'Consultations', 'ms' => 'Konsultasi'],
            ],
            [
                'key' => 'consultations', 'icon' => 'message-square', 'sort' => 0, 'parent' => 'consultations_group',
                'title' => ['id' => 'Konsultasi', 'en' => 'Consultations', 'ms' => 'Konsultasi'],
                'route_name' => 'admin.consultations.index', 'resource' => 'Consultation',
                'module_key' => 'consultations', 'is_crud' => true,
            ],

            // ── Commerce (group) ────────────────────────────────────────
            [
                'key' => 'commerce_group', 'icon' => 'store', 'sort' => 5, 'type' => 'group',
                'title' => ['id' => 'Perdagangan', 'en' => 'Commerce', 'ms' => 'Perdagangan'],
            ],
            [
                'key' => 'products', 'icon' => 'package', 'sort' => 0, 'parent' => 'commerce_group',
                'title' => ['id' => 'Produk', 'en' => 'Products', 'ms' => 'Produk'],
                'route_name' => 'admin.products.index', 'resource' => 'Product',
                'module_key' => 'commerce', 'is_crud' => true,
            ],
            [
                'key' => 'product_categories', 'icon' => 'tags', 'sort' => 1, 'parent' => 'commerce_group',
                'title' => ['id' => 'Kategori Produk', 'en' => 'Product Categories', 'ms' => 'Kategori Produk'],
                'route_name' => 'admin.product_categories.index', 'resource' => 'ProductCategory',
                'module_key' => 'commerce', 'is_crud' => true,
            ],
            [
                'key' => 'orders', 'icon' => 'receipt', 'sort' => 2, 'parent' => 'commerce_group',
                'title' => ['id' => 'Pesanan', 'en' => 'Orders', 'ms' => 'Pesanan'],
                'route_name' => 'admin.orders.index', 'resource' => 'Order',
                'module_key' => 'commerce', 'is_crud' => true,
            ],
            [
                'key' => 'invoices', 'icon' => 'file-text', 'sort' => 3, 'parent' => 'commerce_group',
                'title' => ['id' => 'Invoice', 'en' => 'Invoices', 'ms' => 'Invois'],
                'route_name' => 'admin.invoices.index', 'resource' => 'Invoice',
                'module_key' => 'commerce', 'is_crud' => true,
            ],
            [
                'key' => 'payment_transactions', 'icon' => 'credit-card', 'sort' => 4, 'parent' => 'commerce_group',
                'title' => ['id' => 'Transaksi Pembayaran', 'en' => 'Payment Transactions', 'ms' => 'Transaksi Pembayaran'],
                'route_name' => 'admin.payment_transactions.index', 'resource' => 'PaymentTransaction',
                'module_key' => 'commerce', 'is_crud' => true,
            ],
            [
                'key' => 'inventory_stocks', 'icon' => 'boxes', 'sort' => 5, 'parent' => 'commerce_group',
                'title' => ['id' => 'Stok Inventaris', 'en' => 'Inventory Stocks', 'ms' => 'Stok Inventori'],
                'route_name' => 'admin.inventory_stocks.index', 'resource' => 'InventoryStock',
                'module_key' => 'commerce', 'is_crud' => true,
            ],

            // ── Practitioners (group) ───────────────────────────────────
            [
                'key' => 'practitioners_group', 'icon' => 'stethoscope', 'sort' => 6, 'type' => 'group',
                'title' => ['id' => 'Praktisi', 'en' => 'Practitioners', 'ms' => 'Praktisi'],
            ],
            [
                'key' => 'practitioners', 'icon' => 'user-round-check', 'sort' => 0, 'parent' => 'practitioners_group',
                'title' => ['id' => 'Praktisi', 'en' => 'Practitioners', 'ms' => 'Praktisi'],
                'route_name' => 'admin.practitioners.index', 'resource' => 'Practitioner',
                'module_key' => 'practitioners', 'is_crud' => true,
            ],

            // ── LBA (group) ─────────────────────────────────────────────
            [
                'key' => 'lba_group', 'icon' => 'microscope', 'sort' => 7, 'type' => 'group',
                'title' => ['id' => 'LBA', 'en' => 'LBA', 'ms' => 'LBA'],
            ],
            [
                'key' => 'lba_examinations', 'icon' => 'clipboard-list', 'sort' => 0, 'parent' => 'lba_group',
                'title' => ['id' => 'Pemeriksaan LBA', 'en' => 'LBA Examinations', 'ms' => 'Pemeriksaan LBA'],
                'route_name' => 'admin.lba_examinations.index', 'resource' => 'LbaExamination',
                'module_key' => 'lba', 'is_crud' => true,
            ],
            [
                'key' => 'lba_assessments', 'icon' => 'clipboard-check', 'sort' => 1, 'parent' => 'lba_group',
                'title' => ['id' => 'Asesmen LBA', 'en' => 'LBA Assessments', 'ms' => 'Penilaian LBA'],
                'route_name' => 'admin.lba_assessments.index', 'resource' => 'LbaAssessment',
                'module_key' => 'lba', 'is_crud' => true,
            ],
            [
                'key' => 'lba_images', 'icon' => 'image', 'sort' => 2, 'parent' => 'lba_group',
                'title' => ['id' => 'Gambar LBA', 'en' => 'LBA Images', 'ms' => 'Imej LBA'],
                'route_name' => 'admin.lba_images.index', 'resource' => 'LbaImage',
                'module_key' => 'lba', 'is_crud' => true,
            ],
            [
                'key' => 'lba_report_versions', 'icon' => 'file-badge', 'sort' => 3, 'parent' => 'lba_group',
                'title' => ['id' => 'Laporan LBA', 'en' => 'LBA Reports', 'ms' => 'Laporan LBA'],
                'route_name' => 'admin.lba_report_versions.index', 'resource' => 'LbaReportVersion',
                'module_key' => 'lba', 'is_crud' => true,
            ],
            [
                'key' => 'lba_reviews', 'icon' => 'check-check', 'sort' => 4, 'parent' => 'lba_group',
                'title' => ['id' => 'Review LBA', 'en' => 'LBA Reviews', 'ms' => 'Semakan LBA'],
                'route_name' => 'admin.lba_reviews.index', 'resource' => 'LbaReview',
                'module_key' => 'lba', 'is_crud' => true,
            ],

            // ── Pengaturan (group) ──────────────────────────────────────
            [
                'key' => 'pengaturan', 'icon' => 'settings', 'sort' => 8, 'type' => 'group',
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
                'module_key' => $data['module_key'] ?? null,
            ]);

            $created[$data['key']] = $menu;
        }
    }
}
