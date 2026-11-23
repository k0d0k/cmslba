<?php

return [
    'title'       => 'Jejak Audit',
    'modal_title' => 'Detail Jejak Audit',
    'loading'     => 'Memuat...',
    'load_failed' => 'Gagal memuat detail',
    'all'         => 'Semua',
    'from'        => 'Dari',
    'to'          => 'Sampai',

    // Judul kolom tabel
    'columns' => [
        'created_at'  => 'Waktu',
        'event'       => 'Kejadian',
        'user'        => 'Pengguna',
        'description' => 'Deskripsi',
    ],

    // Label pada modal detail
    'fields' => [
        'created_at'  => 'Waktu',
        'event'       => 'Kejadian',
        'user'        => 'Pengguna',
        'mitra_id'    => 'Mitra',
        'module'      => 'Modul',
        'description' => 'Deskripsi',
        'subject'     => 'Objek',
        'method'      => 'Metode',
        'route_name'  => 'Nama Rute',
        'url'         => 'URL',
        'status_code' => 'Status',
        'ip'          => 'IP',
        'user_agent'  => 'User Agent',
        'properties'  => 'Properti',
    ],

    // Jenis kejadian (dipakai badge & filter)
    'events' => [
        'login'        => 'Login',
        'logout'       => 'Logout',
        'login_failed' => 'Login Gagal',
        'create'       => 'Tambah Data',
        'update'       => 'Ubah Data',
        'delete'       => 'Hapus Data',
        'view'         => 'Lihat Halaman',
        'request'      => 'Aksi',
        'error'        => 'Error',
    ],

    // Ringkasan kejadian login/logout
    'desc_login'           => 'Login berhasil: :email',
    'desc_login_failed'    => 'Percobaan login gagal: :email',
    'desc_logout'          => 'Logout: :email',
    'desc_login_rejected'  => 'Login ditolak: masa langganan mitra tidak aktif (:email)',
];
