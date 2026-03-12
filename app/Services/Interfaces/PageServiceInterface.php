<?php

namespace App\Services\Interfaces;

use App\Models\PageModel;
use Illuminate\Pagination\LengthAwarePaginator;

interface PageServiceInterface
{
    public function getPaginatedList(array $filters): LengthAwarePaginator;

    public function create(array $data): PageModel;

    public function update(PageModel $page, array $data): PageModel;

    public function delete(PageModel $page): void;

    public function bulkDelete(array $ids): void;
}
