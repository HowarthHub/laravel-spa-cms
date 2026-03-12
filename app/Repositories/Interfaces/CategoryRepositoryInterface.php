<?php

namespace App\Repositories\Interfaces;

use App\Models\CategoryModel;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface CategoryRepositoryInterface
{
    public function all(): Collection;

    public function paginate(array $filters): LengthAwarePaginator;

    public function find(int $id): ?CategoryModel;

    public function create(array $data): CategoryModel;

    public function update(CategoryModel $category, array $data): CategoryModel;

    public function delete(CategoryModel $category): void;
}
