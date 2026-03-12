<?php

namespace App\Services\Interfaces;

use App\Models\CategoryModel;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface CategoryServiceInterface
{
    public function getAll(): Collection;

    public function getPaginatedList(array $filters): LengthAwarePaginator;

    public function create(array $data): CategoryModel;

    public function update(CategoryModel $category, array $data): CategoryModel;

    public function delete(CategoryModel $category): void;
}
