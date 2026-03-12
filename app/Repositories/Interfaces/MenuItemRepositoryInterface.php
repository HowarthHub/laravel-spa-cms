<?php

namespace App\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Collection;

interface MenuItemRepositoryInterface
{
    public function getTreeByMenuId(int $menuId): Collection;

    public function deleteByMenuId(int $menuId): void;

    public function insert(array $items): void;
}
