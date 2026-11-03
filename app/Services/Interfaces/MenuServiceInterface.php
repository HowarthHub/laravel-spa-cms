<?php

namespace App\Services\Interfaces;

use App\Models\MenuModel;
use Illuminate\Database\Eloquent\Collection;

interface MenuServiceInterface
{
    public function getAll(): Collection;

    public function getTree(int $menuId): array;

    public function saveTree(int $menuId, array $tree): void;

    public function create(array $data): MenuModel;

    public function update(MenuModel $menu, array $data): MenuModel;

    public function delete(MenuModel $menu): void;
}
