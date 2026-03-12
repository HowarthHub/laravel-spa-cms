<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MenuModel extends Model
{
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
