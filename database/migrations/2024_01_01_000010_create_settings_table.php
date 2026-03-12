<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('group', 100);
            $table->string('key', 100);
            $table->text('value')->nullable();
            $table->enum('type', ['text', 'textarea', 'boolean', 'json', 'image', 'code'])->default('text');
            $table->timestamps();

            $table->unique(['group', 'key']);
        });

        // Essential settings — inserted via migration so they exist on every deployment
        $now = now();
        $settings = [
            // General
            ['group' => 'general', 'key' => 'site_name', 'value' => 'My Site', 'type' => 'text'],
            ['group' => 'general', 'key' => 'tagline', 'value' => '', 'type' => 'text'],
            ['group' => 'general', 'key' => 'logo', 'value' => '', 'type' => 'image'],
            ['group' => 'general', 'key' => 'favicon', 'value' => '', 'type' => 'image'],
            ['group' => 'general', 'key' => 'contact_email', 'value' => '', 'type' => 'text'],
            ['group' => 'general', 'key' => 'phone', 'value' => '', 'type' => 'text'],
            ['group' => 'general', 'key' => 'address', 'value' => '', 'type' => 'textarea'],
            ['group' => 'general', 'key' => 'homepage', 'value' => '', 'type' => 'text'],

            // SEO
            ['group' => 'seo', 'key' => 'meta_title_template', 'value' => '%title% | %site_name%', 'type' => 'text'],
            ['group' => 'seo', 'key' => 'meta_description', 'value' => '', 'type' => 'textarea'],
            ['group' => 'seo', 'key' => 'og_image', 'value' => '', 'type' => 'image'],
            ['group' => 'seo', 'key' => 'tracking_code', 'value' => '', 'type' => 'code'],
            ['group' => 'seo', 'key' => 'robots_txt', 'value' => "User-agent: *\nAllow: /", 'type' => 'textarea'],

            // Social
            ['group' => 'social', 'key' => 'facebook', 'value' => '', 'type' => 'text'],
            ['group' => 'social', 'key' => 'instagram', 'value' => '', 'type' => 'text'],
            ['group' => 'social', 'key' => 'twitter', 'value' => '', 'type' => 'text'],
            ['group' => 'social', 'key' => 'linkedin', 'value' => '', 'type' => 'text'],
            ['group' => 'social', 'key' => 'youtube', 'value' => '', 'type' => 'text'],

            // Schema / Structured Data
            ['group' => 'schema', 'key' => 'organisation_type', 'value' => 'Organization', 'type' => 'text'],
            ['group' => 'schema', 'key' => 'organisation_name', 'value' => '', 'type' => 'text'],
            ['group' => 'schema', 'key' => 'organisation_description', 'value' => '', 'type' => 'textarea'],
            ['group' => 'schema', 'key' => 'organisation_logo', 'value' => '', 'type' => 'image'],
            ['group' => 'schema', 'key' => 'organisation_phone', 'value' => '', 'type' => 'text'],
            ['group' => 'schema', 'key' => 'organisation_email', 'value' => '', 'type' => 'text'],
            ['group' => 'schema', 'key' => 'street_address', 'value' => '', 'type' => 'text'],
            ['group' => 'schema', 'key' => 'city', 'value' => '', 'type' => 'text'],
            ['group' => 'schema', 'key' => 'region', 'value' => '', 'type' => 'text'],
            ['group' => 'schema', 'key' => 'postal_code', 'value' => '', 'type' => 'text'],
            ['group' => 'schema', 'key' => 'country', 'value' => 'GB', 'type' => 'text'],

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
            DB::table('settings')->insert(array_merge($setting, [
                'created_at' => $now,
                'updated_at' => $now,
            ]));
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
