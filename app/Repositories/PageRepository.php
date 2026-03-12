<?php

namespace App\Repositories;

use App\Models\PageModel;
use App\Repositories\Interfaces\PageRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class PageRepository implements PageRepositoryInterface
{
    public function paginateWithFilters(array $filters): LengthAwarePaginator
    {
        return PageModel::query()->paginate(config('cms.per_page.pages'));
    }

    public function find(int $id): ?PageModel
    {
        return PageModel::find($id);
    }

    public function create(array $data): PageModel
    {
        return PageModel::create($data);
    }

    public function update(PageModel $page, array $data): PageModel
    {
        $page->update($data);

        return $page->fresh();
    }

    public function delete(PageModel $page): void
    {
        $page->delete();
    }

    public function bulkDelete(array $ids): void
    {
        PageModel::whereIn('id', $ids)->delete();
    }
}
