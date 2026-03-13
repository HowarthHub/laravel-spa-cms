<?php

return [
    'templates' => [
        'default'      => 'Default',
        'page-builder' => 'Page Builder',
        'landing'      => 'Landing Page',
        'contact'      => 'Contact Page',
        'blog'         => 'Blog',
        'services'     => 'Services',
    ],

    'public_per_page' => [
        'posts'    => 12,
        'services' => 12,
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

    'block_types' => [
        'hero'         => ['label' => 'Hero Banner'],
        'richText'     => ['label' => 'Rich Text'],
        'image'        => ['label' => 'Image'],
        'twoColumn'    => ['label' => 'Two Columns'],
        'threeColumn'  => ['label' => 'Three Columns'],
        'callToAction' => ['label' => 'Call to Action'],
        'featureGrid'  => ['label' => 'Feature Grid'],
        'testimonial'  => ['label' => 'Testimonial'],
        'spacer'       => ['label' => 'Spacer'],
        'checklist'    => ['label' => 'Checklist'],
    ],

    'media' => [
        'disk'          => env('MEDIA_DISK', 'public'),
        'max_size_kb'   => 10240,
        'allowed_mimes' => ['image/jpeg', 'image/png', 'image/webp', 'image/gif', 'application/pdf'],
    ],
];
