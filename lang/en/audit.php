<?php

return [
    'title'       => 'Audit Trail',
    'modal_title' => 'Audit Trail Detail',
    'loading'     => 'Loading...',
    'load_failed' => 'Failed to load detail',
    'all'         => 'All',
    'from'        => 'From',
    'to'          => 'To',

    // Table column titles
    'columns' => [
        'created_at'  => 'Time',
        'event'       => 'Event',
        'user'        => 'User',
        'description' => 'Description',
    ],

    // Labels inside the detail modal
    'fields' => [
        'created_at'  => 'Time',
        'event'       => 'Event',
        'user'        => 'User',
        'mitra_id'    => 'Tenant',
        'module'      => 'Module',
        'description' => 'Description',
        'subject'     => 'Subject',
        'method'      => 'Method',
        'route_name'  => 'Route Name',
        'url'         => 'URL',
        'status_code' => 'Status',
        'ip'          => 'IP',
        'user_agent'  => 'User Agent',
        'properties'  => 'Properties',
    ],

    // Event types (used by badge & filter)
    'events' => [
        'login'        => 'Login',
        'logout'       => 'Logout',
        'login_failed' => 'Login Failed',
        'create'       => 'Create Record',
        'update'       => 'Update Record',
        'delete'       => 'Delete Record',
        'view'         => 'View Page',
        'request'      => 'Action',
        'error'        => 'Error',
    ],

    // Login/logout summary descriptions
    'desc_login'           => 'Login successful: :email',
    'desc_login_failed'    => 'Login attempt failed: :email',
    'desc_logout'          => 'Logout: :email',
    'desc_login_rejected'  => 'Login rejected: tenant subscription is not active (:email)',
];
