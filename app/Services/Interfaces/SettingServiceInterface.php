<?php

namespace App\Services\Interfaces;

interface SettingServiceInterface
{
    public function get(string $groupAndKey, mixed $default = null): mixed;

    public function set(string $groupAndKey, mixed $value): void;

    public function group(string $group): array;

    public function all(): array;
}
