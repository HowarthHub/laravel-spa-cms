<?php

namespace App\Repositories\Interfaces;

use App\Models\PostModel;
use Illuminate\Pagination\LengthAwarePaginator;

interface PostRepositoryInterface
{
    public function paginateWithFilters(array $filters): LengthAwarePaginator;

    public function find(int $id): ?PostModel;

    public function create(array $data): PostModel;

    public function update(PostModel $post, array $data): PostModel;

    public function delete(PostModel $post): void;

    public function bulkDelete(array $ids): void;

    public function recentPublished(int $limit): \Illuminate\Database\Eloquent\Collection;
}
