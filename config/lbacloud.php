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
        'default' => ['label' => 'Default', 'primary' => '#465fff', 'sidebar' => '#1c2434'],
        'emerald' => ['label' => 'Emerald', 'primary' => '#10b981', 'sidebar' => '#062a22'],
        'rose'    => ['label' => 'Rose',    'primary' => '#f43f5e', 'sidebar' => '#2a0a14'],
        'slate'   => ['label' => 'Slate',   'primary' => '#0ea5e9', 'sidebar' => '#0f172a'],
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
