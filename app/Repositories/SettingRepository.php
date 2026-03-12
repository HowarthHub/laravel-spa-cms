<?php

namespace App\Repositories;

use App\Models\SettingModel;
use App\Repositories\Interfaces\SettingRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class SettingRepository implements SettingRepositoryInterface
{
    public function all(): Collection
    {
        return SettingModel::all();
    }

    public function getByGroup(string $group): Collection
    {
        return SettingModel::where('group', $group)->get();
    }

    public function getValue(string $group, string $key): ?string
    {
        return SettingModel::where('group', $group)
            ->where('key', $key)
            ->value('value');
    }

    public function upsert(string $group, string $key, ?string $value): void
    {
        SettingModel::updateOrCreate(
            ['group' => $group, 'key' => $key],
            ['value' => $value]
        );
    }
}
