<?php

namespace App\Services\Interfaces;

use App\Models\PostModel;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface PostServiceInterface
{
    public function getPaginatedList(array $filters): LengthAwarePaginator;

    public function create(array $data): PostModel;

    public function update(PostModel $post, array $data): PostModel;

    public function delete(PostModel $post): void;

    public function bulkDelete(array $ids): void;

    public function bulkDraft(array $ids): void;

    public function bulkPublish(array $ids): void;

    public function recentPublished(int $limit = 5): Collection;
}
