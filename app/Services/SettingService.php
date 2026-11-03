<?php

namespace App\Services;

use App\Repositories\Interfaces\SettingRepositoryInterface;
use App\Services\Interfaces\SettingServiceInterface;
use Illuminate\Support\Facades\Cache;

class SettingService implements SettingServiceInterface
{
    public function __construct(
        private readonly SettingRepositoryInterface $settingRepository
    ) {}

    public function get(string $groupAndKey, mixed $default = null): mixed
    {
        [$group, $key] = $this->parseKey($groupAndKey);

        $settings = $this->all();

        return $settings[$group][$key] ?? $default;
    }

    public function set(string $groupAndKey, mixed $value): void
    {
        [$group, $key] = $this->parseKey($groupAndKey);

        $this->settingRepository->upsert($group, $key, $value);

        Cache::forget('cms_settings');
        Cache::forget("setting.{$group}.{$key}");
    }

    public function group(string $group): array
    {
        $all = $this->all();

        return $all[$group] ?? [];
    }

    public function all(): array
    {
        return Cache::rememberForever('cms_settings', function () {
            $settings = [];

            foreach ($this->settingRepository->all() as $setting) {
                $settings[$setting->group][$setting->key] = $setting->value;
            }

            return $settings;
        });
    }

    private function parseKey(string $groupAndKey): array
    {
        $parts = explode('.', $groupAndKey, 2);

        if (count($parts) !== 2) {
            throw new \InvalidArgumentException("Setting key must be in 'group.key' format, got: {$groupAndKey}");
        }

        return $parts;
    }
}
