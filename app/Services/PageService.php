<?php

namespace App\Services;

use App\Models\PageModel;
use App\Repositories\Interfaces\PageRepositoryInterface;
use App\Services\Interfaces\PageServiceInterface;
use App\Services\Interfaces\SlugServiceInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class PageService implements PageServiceInterface
{
    public function __construct(
        private readonly PageRepositoryInterface $pageRepository,
        private readonly SlugServiceInterface $slugService
    ) {}

    public function getPaginatedList(array $filters): LengthAwarePaginator
    {
        return $this->pageRepository->paginateWithFilters($filters);
    }

    public function create(array $data): PageModel
    {
        $data['slug'] = $this->slugService->generate($data['title'], PageModel::class);
        $data['author_id'] = auth()->id();

        return $this->pageRepository->create($data);
    }

    public function update(PageModel $page, array $data): PageModel
    {
        if (isset($data['title']) && $data['title'] !== $page->title) {
            $data['slug'] = $this->slugService->generate($data['title'], PageModel::class, $page->id);
        }

        $data['updated_by'] = auth()->id();

        return $this->pageRepository->update($page, $data);
    }

    public function delete(PageModel $page): void
    {
        $this->pageRepository->delete($page);
    }

    public function bulkDelete(array $ids): void
    {
        $this->pageRepository->bulkDelete($ids);
    }
}
