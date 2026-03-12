<?php

namespace App\Services;

use App\Models\TagModel;
use App\Repositories\Interfaces\TagRepositoryInterface;
use App\Services\Interfaces\SlugServiceInterface;
use App\Services\Interfaces\TagServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class TagService implements TagServiceInterface
{
    public function __construct(
        private readonly TagRepositoryInterface $tagRepository,
        private readonly SlugServiceInterface $slugService
    ) {}

    public function getAll(): Collection
    {
        return $this->tagRepository->all();
    }

    public function getPaginatedList(array $filters): LengthAwarePaginator
    {
        return $this->tagRepository->paginate($filters);
    }

    public function create(array $data): TagModel
    {
        $data['slug'] = $this->slugService->generate($data['name'], TagModel::class);

        return $this->tagRepository->create($data);
    }

    public function delete(TagModel $tag): void
    {
        $this->tagRepository->delete($tag);
    }
}
