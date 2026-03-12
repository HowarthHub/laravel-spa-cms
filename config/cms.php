<?php

return [
    'templates' => [
        'default'    => 'Default',
        'full-width' => 'Full Width',
        'landing'    => 'Landing Page',
        'contact'    => 'Contact Page',
    ],

    'per_page' => [
        'pages'     => 20,
        'posts'     => 20,
        'enquiries' => 25,
        'media'     => 40,
        'forms'     => 20,
        'services'  => 20,
        'users'     => 20,
    ],

    'media' => [
        'disk'          => env('MEDIA_DISK', 'public'),
        'max_size_kb'   => 10240,
        'allowed_mimes' => ['image/jpeg', 'image/png', 'image/webp', 'image/gif', 'application/pdf'],
    ],
];
