<?php

return [
    'title'       => 'Jejak Audit',
    'modal_title' => 'Butiran Jejak Audit',
    'loading'     => 'Memuatkan...',
    'load_failed' => 'Gagal memuatkan butiran',
    'all'         => 'Semua',
    'from'        => 'Dari',
    'to'          => 'Hingga',

    // Tajuk lajur jadual
    'columns' => [
        'created_at'  => 'Masa',
        'event'       => 'Kejadian',
        'user'        => 'Pengguna',
        'description' => 'Penerangan',
    ],

    // Label dalam modal butiran
    'fields' => [
        'created_at'  => 'Masa',
        'event'       => 'Kejadian',
        'user'        => 'Pengguna',
        'mitra_id'    => 'Mitra',
        'module'      => 'Modul',
        'description' => 'Penerangan',
        'subject'     => 'Subjek',
        'method'      => 'Kaedah',
        'route_name'  => 'Nama Laluan',
        'url'         => 'URL',
        'status_code' => 'Status',
        'ip'          => 'IP',
        'user_agent'  => 'User Agent',
        'properties'  => 'Properti',
    ],

    // Jenis kejadian (digunakan oleh badge & penapis)
    'events' => [
        'login'        => 'Log Masuk',
        'logout'       => 'Log Keluar',
        'login_failed' => 'Log Masuk Gagal',
        'create'       => 'Tambah Data',
        'update'       => 'Kemas Kini Data',
        'delete'       => 'Padam Data',
        'view'         => 'Lihat Halaman',
        'request'      => 'Tindakan',
        'error'        => 'Ralat',
    ],

    // Ringkasan kejadian log masuk/keluar
    'desc_login'           => 'Log masuk berjaya: :email',
    'desc_login_failed'    => 'Percubaan log masuk gagal: :email',
    'desc_logout'          => 'Log keluar: :email',
    'desc_login_rejected'  => 'Log masuk ditolak: langganan mitra tidak aktif (:email)',
];
