<?php

return [
    'title'          => 'CRUD Generator',
    'title_new'      => 'CRUD Generator - Create New',
    'files_created'  => 'Generated files',
    'definitions'    => 'CRUD Definitions',
    'new'            => 'Create New CRUD',
    'col_model'      => 'Model',
    'col_table'      => 'Table',
    'col_connection' => 'Connection',
    'col_menu'       => 'Menu',
    'col_roles'      => 'Roles',

    'module_info'    => 'Module Information',
    'menu_title'     => 'Menu Title',
    'model'          => 'Model',
    'model_hint'     => 'StudlyCase, without spaces. Model: App\\Models\\Tenant\\{Model}',
    'key_route'      => 'Key / Route (optional)',
    'database'       => 'Database',
    'db_tenant'      => 'Tenant Database (tenant)',
    'db_central'     => 'Central Database (central)',
    'db_hint'        => '<b>Tenant</b>: table is created in <code>database/migrations/tenant</code> (model <code>App\\Models\\Tenant</code>), each table gets a <code>mitra_uuid</code>. <b>Central</b>: table is created in <code>database/migrations</code> (model <code>App\\Models</code>).',
    'parent_menu'    => 'Parent Menu',
    'include_cabang' => 'Include Branch relation (multi branch)',

    'field'          => 'Fields',
    'add_field'      => '+ Add Field',
    'field_note'     => 'A <b>slug</b> column is automatically added to every table. For the <b>Select</b> type, fill the options in <code>key: Label</code> format, one per line.',

    'assign_role'    => 'Assign to Roles',
    'assign_hint'    => 'Admin Principal &amp; User Principal are automatically granted full access.',

    'generate'       => 'Generate CRUD',
    'field_name'     => 'Field Name',
    'field_type'     => 'Type',
    'field_label'    => 'Label',
    'nullable'       => 'Nullable',
    'unique'         => 'Unique',
    'remove'         => 'Remove',
    'options'        => 'Options (Select only)',
    'per_line'       => 'per line',

    'menu_placeholder'  => 'Product',
    'field_name_placeholder' => 'name',
    'field_label_placeholder' => 'Name',
];
