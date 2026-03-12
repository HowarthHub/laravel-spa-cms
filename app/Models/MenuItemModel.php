<?php

namespace App\Models;

use Database\Factories\MenuItemModelFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class MenuItemModel extends Model
{
    /** @use HasFactory<MenuItemModelFactory> */
    use HasFactory;

    protected static function newFactory(): MenuItemModelFactory
    {
        return MenuItemModelFactory::new();
    }

    protected $table = 'menu_items';

    protected $fillable = [
        'menu_id',
        'parent_id',
        'label',
        'type',
        'linkable_type',
        'linkable_id',
        'url',
        'target',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    public function menu(): BelongsTo
    {
        return $this->belongsTo(MenuModel::class, 'menu_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort_order');
    }

    public function linkable(): MorphTo
    {
        return $this->morphTo();
    }
}
