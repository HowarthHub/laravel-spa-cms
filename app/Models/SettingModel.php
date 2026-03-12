<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SettingModel extends Model
{
    protected $table = 'settings';

    protected $fillable = [
        'group',
        'key',
        'value',
        'type',
    ];

    public static function getCached(string $group, string $key, ?string $default = null): ?string
    {
        return Cache::remember("setting.{$group}.{$key}", 3600, function () use ($group, $key, $default) {
            return static::where('group', $group)->where('key', $key)->value('value') ?? $default;
        });
    }
}
