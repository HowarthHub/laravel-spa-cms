<?php

namespace App\Models;

use Database\Factories\MenuModelFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MenuModel extends Model
{
    /** @use HasFactory<MenuModelFactory> */
    use HasFactory;

    protected static function newFactory(): MenuModelFactory
    {
        return MenuModelFactory::new();
    }

    protected $table = 'menus';

    protected $fillable = [
        'name',
        'handle',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(MenuItemModel::class, 'menu_id')->orderBy('sort_order');
    }
}
