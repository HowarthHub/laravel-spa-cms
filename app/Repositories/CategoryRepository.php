<?php

namespace App\Repositories;

use App\Models\CategoryModel;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function all(): Collection
    {
        return CategoryModel::orderBy('sort_order')->orderBy('name')->get();
    }

    public function paginate(array $filters): LengthAwarePaginator
    {
        return CategoryModel::query()
            ->withCount('posts')
            ->when($filters['search'] ?? null, fn ($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(config('cms.per_page.pages'));
    }

    public function find(int $id): ?CategoryModel
    {
        return CategoryModel::find($id);
    }

    public function create(array $data): CategoryModel
    {
        return CategoryModel::create($data);
    }

    public function update(CategoryModel $category, array $data): CategoryModel
    {
        $category->update($data);

        return $category->fresh();
    }

    public function delete(CategoryModel $category): void
    {
        $category->delete();
    }
}
