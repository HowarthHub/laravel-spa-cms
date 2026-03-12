<?php

namespace App\Repositories;

use App\Models\MenuItemModel;
use App\Repositories\Interfaces\MenuItemRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class MenuItemRepository implements MenuItemRepositoryInterface
{
    public function getTreeByMenuId(int $menuId): Collection
    {
        return MenuItemModel::where('menu_id', $menuId)
            ->whereNull('parent_id')
            ->with('children')
            ->orderBy('sort_order')
            ->get();
    }

    public function deleteByMenuId(int $menuId): void
    {
        MenuItemModel::where('menu_id', $menuId)->delete();
    }

    public function insert(array $items): void
    {
        foreach ($items as $item) {
            MenuItemModel::create($item);
        }
    }
}
