<?php

namespace App\Repositories\Interfaces;

use App\Models\MenuModel;
use Illuminate\Database\Eloquent\Collection;

interface MenuRepositoryInterface
{
    public function all(): Collection;

    public function find(int $id): ?MenuModel;

    public function findByHandle(string $handle): ?MenuModel;

    public function create(array $data): MenuModel;

    public function update(MenuModel $menu, array $data): MenuModel;

    public function delete(MenuModel $menu): void;
}
