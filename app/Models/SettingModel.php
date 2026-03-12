<?php

namespace App\Models;

use Database\Factories\SettingModelFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SettingModel extends Model
{
    /** @use HasFactory<SettingModelFactory> */
    use HasFactory;

    protected static function newFactory(): SettingModelFactory
    {
        return SettingModelFactory::new();
    }

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
