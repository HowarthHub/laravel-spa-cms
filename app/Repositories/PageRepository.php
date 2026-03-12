<?php

namespace App\Repositories;

use App\Models\PageModel;
use App\Repositories\Interfaces\PageRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class PageRepository implements PageRepositoryInterface
{
    public function paginateWithFilters(array $filters): LengthAwarePaginator
    {
        return PageModel::query()
            ->with(['author', 'parent'])
            ->when($filters['search'] ?? null, fn ($q, $s) => $q->where('title', 'like', "%{$s}%"))
            ->when($filters['status'] ?? null, fn ($q, $s) => $q->where('status', $s))
            ->latest('updated_at')
            ->paginate(config('cms.per_page.pages'));
    }

    public function find(int $id): ?PageModel
    {
        return PageModel::find($id);
    }

    public function all(): Collection
    {
        return PageModel::orderBy('title')->get();
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
