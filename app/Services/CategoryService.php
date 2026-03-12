<?php

namespace App\Services;

use App\Models\CategoryModel;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use App\Services\Interfaces\CategoryServiceInterface;
use App\Services\Interfaces\SlugServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class CategoryService implements CategoryServiceInterface
{
    public function __construct(
        private readonly CategoryRepositoryInterface $categoryRepository,
        private readonly SlugServiceInterface $slugService
    ) {}

    public function getAll(): Collection
    {
        return $this->categoryRepository->all();
    }

    public function getPaginatedList(array $filters): LengthAwarePaginator
    {
        return $this->categoryRepository->paginate($filters);
    }

    public function create(array $data): CategoryModel
    {
        $data['slug'] = $this->slugService->generate($data['name'], CategoryModel::class);

        return $this->categoryRepository->create($data);
    }

    public function update(CategoryModel $category, array $data): CategoryModel
    {
        if (isset($data['name']) && $data['name'] !== $category->name) {
            $data['slug'] = $this->slugService->generate($data['name'], CategoryModel::class, $category->id);
        }

        return $this->categoryRepository->update($category, $data);
    }

    public function delete(CategoryModel $category): void
    {
        $this->categoryRepository->delete($category);
    }
}
