<?php

namespace App\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Collection;

interface SettingRepositoryInterface
{
    public function all(): Collection;

    public function getByGroup(string $group): Collection;

    public function getValue(string $group, string $key): ?string;

    public function upsert(string $group, string $key, ?string $value): void;
}
