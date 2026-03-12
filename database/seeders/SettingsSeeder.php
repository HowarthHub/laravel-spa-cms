<?php

namespace Database\Seeders;

use App\Models\SettingModel;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // General
            ['group' => 'general', 'key' => 'site_name', 'value' => 'My Site', 'type' => 'text'],
            ['group' => 'general', 'key' => 'tagline', 'value' => '', 'type' => 'text'],
            ['group' => 'general', 'key' => 'logo', 'value' => '', 'type' => 'image'],
            ['group' => 'general', 'key' => 'favicon', 'value' => '', 'type' => 'image'],
            ['group' => 'general', 'key' => 'contact_email', 'value' => '', 'type' => 'text'],
            ['group' => 'general', 'key' => 'phone', 'value' => '', 'type' => 'text'],
            ['group' => 'general', 'key' => 'address', 'value' => '', 'type' => 'textarea'],

            // SEO
            ['group' => 'seo', 'key' => 'meta_title_template', 'value' => '%title% | %site_name%', 'type' => 'text'],
            ['group' => 'seo', 'key' => 'meta_description', 'value' => '', 'type' => 'textarea'],
            ['group' => 'seo', 'key' => 'og_image', 'value' => '', 'type' => 'image'],
            ['group' => 'seo', 'key' => 'google_analytics_id', 'value' => '', 'type' => 'text'],
            ['group' => 'seo', 'key' => 'robots_txt', 'value' => "User-agent: *\nAllow: /", 'type' => 'textarea'],

            // Social
            ['group' => 'social', 'key' => 'facebook', 'value' => '', 'type' => 'text'],
            ['group' => 'social', 'key' => 'instagram', 'value' => '', 'type' => 'text'],
            ['group' => 'social', 'key' => 'twitter', 'value' => '', 'type' => 'text'],
            ['group' => 'social', 'key' => 'linkedin', 'value' => '', 'type' => 'text'],
            ['group' => 'social', 'key' => 'youtube', 'value' => '', 'type' => 'text'],

            // Mail
            ['group' => 'mail', 'key' => 'driver', 'value' => 'smtp', 'type' => 'text'],
            ['group' => 'mail', 'key' => 'host', 'value' => '', 'type' => 'text'],
            ['group' => 'mail', 'key' => 'port', 'value' => '587', 'type' => 'text'],
            ['group' => 'mail', 'key' => 'username', 'value' => '', 'type' => 'text'],
            ['group' => 'mail', 'key' => 'password', 'value' => '', 'type' => 'text'],
            ['group' => 'mail', 'key' => 'from_name', 'value' => '', 'type' => 'text'],
            ['group' => 'mail', 'key' => 'from_email', 'value' => '', 'type' => 'text'],
        ];

        foreach ($settings as $setting) {
            SettingModel::firstOrCreate(
                ['group' => $setting['group'], 'key' => $setting['key']],
                $setting
            );
        }
    }
}
