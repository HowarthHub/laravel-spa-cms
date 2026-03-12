<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'CMS') }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @inertiaHead
        {!! \App\Models\SettingModel::getCached('seo', 'tracking_code') !!}
        @php
            $schemaName = \App\Models\SettingModel::getCached('schema', 'organisation_name');
        @endphp
        @if($schemaName)
        <script type="application/ld+json">
        {!! json_encode(array_filter([
            '@context' => 'https://schema.org',
            '@type' => \App\Models\SettingModel::getCached('schema', 'organisation_type', 'Organisation'),
            'name' => $schemaName,
            'description' => \App\Models\SettingModel::getCached('schema', 'organisation_description'),
            'logo' => \App\Models\SettingModel::getCached('schema', 'organisation_logo'),
            'telephone' => \App\Models\SettingModel::getCached('schema', 'organisation_phone'),
            'email' => \App\Models\SettingModel::getCached('schema', 'organisation_email'),
            'url' => config('app.url'),
            'address' => array_filter([
                '@type' => 'PostalAddress',
                'streetAddress' => \App\Models\SettingModel::getCached('schema', 'street_address'),
                'addressLocality' => \App\Models\SettingModel::getCached('schema', 'city'),
                'addressRegion' => \App\Models\SettingModel::getCached('schema', 'region'),
                'postalCode' => \App\Models\SettingModel::getCached('schema', 'postal_code'),
                'addressCountry' => \App\Models\SettingModel::getCached('schema', 'country'),
            ]) ?: null,
            'sameAs' => array_values(array_filter([
                \App\Models\SettingModel::getCached('social', 'facebook'),
                \App\Models\SettingModel::getCached('social', 'instagram'),
                \App\Models\SettingModel::getCached('social', 'twitter'),
                \App\Models\SettingModel::getCached('social', 'linkedin'),
                \App\Models\SettingModel::getCached('social', 'youtube'),
            ])) ?: null,
        ]), JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
        </script>
        @endif
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
