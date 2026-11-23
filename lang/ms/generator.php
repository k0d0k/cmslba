<?php

return [
    'title'          => 'CRUD Generator',
    'title_new'      => 'CRUD Generator - Cipta Baharu',
    'files_created'  => 'Fail yang dijana',
    'definitions'    => 'Definisi CRUD',
    'new'            => 'Cipta CRUD Baharu',
    'col_model'      => 'Model',
    'col_table'      => 'Jadual',
    'col_connection' => 'Sambungan',
    'col_menu'       => 'Menu',
    'col_roles'      => 'Peranan',

    'module_info'    => 'Maklumat Modul',
    'menu_title'     => 'Tajuk Menu',
    'model'          => 'Model',
    'model_hint'     => 'StudlyCase, tanpa ruang. Model: App\\Models\\Tenant\\{Model}',
    'key_route'      => 'Key / Route (pilihan)',
    'database'       => 'Pangkalan Data',
    'db_tenant'      => 'Pangkalan Data Mitra (tenant)',
    'db_central'     => 'Pangkalan Data Pusat (central)',
    'db_hint'        => '<b>Mitra</b>: jadual dicipta di <code>database/migrations/tenant</code> (model <code>App\\Models\\Tenant</code>), setiap jadual diberi <code>mitra_uuid</code>. <b>Pusat</b>: jadual dicipta di <code>database/migrations</code> (model <code>App\\Models</code>).',
    'parent_menu'    => 'Menu Induk',
    'include_cabang' => 'Sertakan hubungan Cawangan (pelbagai cawangan)',

    'field'          => 'Medan',
    'add_field'      => '+ Tambah Medan',
    'field_note'     => 'Lajur <b>slug</b> ditambah secara automatik pada setiap jadual. Untuk jenis <b>Select</b>, isi pilihan dengan format <code>key: Label</code> setiap baris.',

    'assign_role'    => 'Tetapkan kepada Peranan',
    'assign_hint'    => 'Admin Principal &amp; User Principal diberi akses penuh secara automatik.',

    'generate'       => 'Jana CRUD',
    'field_name'     => 'Nama Medan',
    'field_type'     => 'Jenis',
    'field_label'    => 'Label',
    'nullable'       => 'Nullable',
    'unique'         => 'Unik',
    'remove'         => 'Padam',
    'options'        => 'Pilihan (Select sahaja)',
    'per_line'       => 'setiap baris',

    'menu_placeholder'  => 'Produk',
    'field_name_placeholder' => 'name',
    'field_label_placeholder' => 'Nama',
];
