<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Kode role bawaan (7 role sesuai spesifikasi)
    |--------------------------------------------------------------------------
    */
    'roles' => [
        'admin_principal'   => [
            'label'      => 'Admin Principal',
            'scope'      => 'principal',
            'description' => 'Akses penuh ke seluruh sistem (pusat).',
        ],
        'user_principal'    => [
            'label'      => 'User Principal',
            'scope'      => 'principal',
            'description' => 'Akses terbatas di level pusat.',
        ],
        'pemilik_mitra'     => [
            'label'      => 'Pemilik Mitra',
            'scope'      => 'mitra',
            'description' => 'Pemilik mitra, akses data mitra & seluruh cabangnya.',
        ],
        'admin_mitra'       => [
            'label'      => 'Admin Mitra',
            'scope'      => 'mitra',
            'description' => 'Administrator mitra, akses data mitra & seluruh cabangnya.',
        ],
        'user_mitra'        => [
            'label'      => 'User Mitra',
            'scope'      => 'mitra',
            'description' => 'Akun user pelanggan/mitra yang bisa login ke panel mitra.',
        ],
        'staff_admin'       => [
            'label'      => 'Staff Admin',
            'scope'      => 'mitra',
            'description' => 'Staff administrasi mitra (multi cabang).',
        ],
        'operator'          => [
            'label'      => 'Operator LBA/Dokter/Peninjau/Konsultan',
            'scope'      => 'mitra',
            'description' => 'Operator lapangan mitra (multi cabang).',
        ],
        'keuangan_penjualan' => [
            'label'      => 'Keuangan & Penjualan',
            'scope'      => 'mitra',
            'description' => 'Bagian keuangan & penjualan mitra (multi cabang).',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Permission CRUD yang bisa di-assign ke setiap menu
    |--------------------------------------------------------------------------
    */
    'abilities' => ['view', 'create', 'update', 'delete', 'export'],

    /*
    |--------------------------------------------------------------------------
    | Bahasa yang didukung
    |--------------------------------------------------------------------------
    */
    'locales' => [
        'id' => ['label' => 'Indonesia', 'flag' => 'ID'],
        'en' => ['label' => 'English',   'flag' => 'EN'],
        'ms' => ['label' => 'Malaysia',  'flag' => 'MS'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Skin bawaan
    |--------------------------------------------------------------------------
    */
    'skins' => [
        // Default mengikuti identitas LBA Cloud (navy + teal) seperti dev.lbacloud.com.
        'default'  => ['label' => 'LBA Cloud',    'primary' => '#087f80', 'sidebar' => '#14263d'],
        'midnight' => ['label' => 'Midnight',     'primary' => '#c9a227', 'sidebar' => '#0c1725'],
        'obsidian' => ['label' => 'Obsidian',     'primary' => '#d4af7a', 'sidebar' => '#0a0a0b'],
        'royal'    => ['label' => 'Royal Indigo', 'primary' => '#7c5cff', 'sidebar' => '#1e1b4b'],
        'emerald'  => ['label' => 'Emerald',      'primary' => '#10b981', 'sidebar' => '#062a22'],
        'slate'    => ['label' => 'Slate',        'primary' => '#0ea5e9', 'sidebar' => '#0f172a'],
        'rose'     => ['label' => 'Rose',         'primary' => '#f43f5e', 'sidebar' => '#2a0a14'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Prefix nama database mitra (tenant)
    |--------------------------------------------------------------------------
    */
    'tenant_db_prefix' => env('TENANT_DB_PREFIX', 'lbacloud_mitra_'),

    /*
    |--------------------------------------------------------------------------
    | Path migrasi khusus database mitra
    |--------------------------------------------------------------------------
    */
    'tenant_migration_path' => database_path('migrations/tenant'),
];
