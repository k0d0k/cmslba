<?php

return [
    'title'          => 'CRUD Generator',
    'title_new'      => 'CRUD Generator - Buat Baru',
    'files_created'  => 'File yang dibuat',
    'definitions'    => 'Definisi CRUD',
    'new'            => 'Buat CRUD Baru',
    'col_model'      => 'Model',
    'col_table'      => 'Tabel',
    'col_connection' => 'Koneksi',
    'col_menu'       => 'Menu',
    'col_roles'      => 'Roles',

    'module_info'    => 'Informasi Modul',
    'menu_title'     => 'Judul Menu',
    'model'          => 'Model',
    'model_hint'     => 'StudlyCase, tanpa spasi. Model: App\\Models\\Tenant\\{Model}',
    'key_route'      => 'Key / Route (opsional)',
    'database'       => 'Database',
    'db_tenant'      => 'Database Mitra (tenant)',
    'db_central'     => 'Database Pusat (central)',
    'db_hint'        => '<b>Mitra</b>: tabel dibuat di <code>database/migrations/tenant</code> (model <code>App\\Models\\Tenant</code>), tiap tabel diberi <code>mitra_uuid</code>. <b>Pusat</b>: tabel dibuat di <code>database/migrations</code> (model <code>App\\Models</code>).',
    'parent_menu'    => 'Parent Menu',
    'include_cabang' => 'Sertakan relasi Cabang (multi cabang)',

    'field'          => 'Field',
    'add_field'      => '+ Tambah Field',
    'field_note'     => 'Kolom <b>slug</b> otomatis ditambahkan ke setiap tabel. Untuk tipe <b>Select</b>, isi opsi dengan format <code>key: Label</code> per baris.',

    'assign_role'    => 'Assign ke Role',
    'assign_hint'    => 'Admin Principal &amp; User Principal otomatis diberi akses penuh.',

    'generate'       => 'Generate CRUD',
    'field_name'     => 'Nama Field',
    'field_type'     => 'Tipe',
    'field_label'    => 'Label',
    'nullable'       => 'Nullable',
    'unique'         => 'Unik',
    'remove'         => 'Hapus',
    'options'        => 'Opsi (khusus Select)',
    'per_line'       => 'per baris',

    'menu_placeholder'  => 'Produk',
    'field_name_placeholder' => 'name',
    'field_label_placeholder' => 'Nama',
];
