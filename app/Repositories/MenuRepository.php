<?php

namespace App\Repositories;

use App\Models\MenuModel;
use App\Repositories\Interfaces\MenuRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class MenuRepository implements MenuRepositoryInterface
{
    public function all(): Collection
    {
        return MenuModel::orderBy('name')->get();
    }

    public function find(int $id): ?MenuModel
    {
        return MenuModel::find($id);
    }

    public function findByHandle(string $handle): ?MenuModel
    {
        return MenuModel::where('handle', $handle)->first();
    }

    public function create(array $data): MenuModel
    {
        return MenuModel::create($data);
    }

    public function delete(MenuModel $menu): void
    {
        $menu->delete();
    }
}
