<?php

namespace App\Repositories\Interfaces;

use App\Models\PageModel;
use Illuminate\Pagination\LengthAwarePaginator;

interface PageRepositoryInterface
{
    public function paginateWithFilters(array $filters): LengthAwarePaginator;

    public function find(int $id): ?PageModel;

    public function create(array $data): PageModel;

    public function update(PageModel $page, array $data): PageModel;

    public function delete(PageModel $page): void;

    public function bulkDelete(array $ids): void;
}
