<?php

use App\Services\Interfaces\SettingServiceInterface;
use Illuminate\Support\Facades\Cache;

beforeEach(function () {
    $this->settingService = app(SettingServiceInterface::class);
});

it('gets a setting value', function () {
    // general.site_name is seeded by migration
    $value = $this->settingService->get('general.site_name');

    expect($value)->not->toBeNull();
});

it('returns default when setting does not exist', function () {
    $value = $this->settingService->get('nonexistent.key', 'default');

    expect($value)->toBe('default');
});

it('sets a setting value and clears cache', function () {
    Cache::forget('cms_settings');

    $this->settingService->set('general.site_name', 'Updated Name');

    // Cache should be cleared, so a fresh fetch should return updated value
    $value = $this->settingService->get('general.site_name');
    expect($value)->toBe('Updated Name');
});

it('retrieves settings by group', function () {
    $group = $this->settingService->group('general');

    expect($group)->toBeArray();
    expect($group)->toHaveKey('site_name');
});

it('throws for invalid key format', function () {
    $this->settingService->get('no-dot-format');
})->throws(InvalidArgumentException::class);
