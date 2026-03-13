<?php

use App\Models\SettingModel;

// --- Index ---

it('shows settings grouped by group', function () {
    actingAsSuperAdmin();

    // Settings are seeded by migration, so they already exist
    $this->get(route('admin.settings.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Admin/Settings/SettingIndexPage')
            ->has('settings.general')
            ->has('settings.seo')
            ->has('pages')
        );
});

// --- Update ---

it('updates settings by group.key', function () {
    actingAsSuperAdmin();

    $this->put(route('admin.settings.update'), [
        'settings' => ['general.site_name' => 'New Name'],
        'tab' => 'general',
    ])->assertRedirect(route('admin.settings.index', ['tab' => 'general']));

    expect(SettingModel::where('group', 'general')->where('key', 'site_name')->value('value'))
        ->toBe('New Name');
});

it('preserves tab parameter on redirect', function () {
    actingAsSuperAdmin();

    $this->put(route('admin.settings.update'), [
        'settings' => ['seo.robots' => 'index,follow'],
        'tab' => 'seo',
    ])->assertRedirect(route('admin.settings.index', ['tab' => 'seo']));
});

// --- Auth ---

it('denies index access without manage settings permission', function () {
    actingAsEditor();

    $this->get(route('admin.settings.index'))
        ->assertForbidden();
});

it('denies update without manage settings permission', function () {
    actingAsEditor();

    $this->put(route('admin.settings.update'), [
        'settings' => ['general.site_name' => 'Hacked'],
    ])->assertForbidden();
});

it('redirects guests to login', function () {
    $this->get(route('admin.settings.index'))
        ->assertRedirect(route('login'));
});
