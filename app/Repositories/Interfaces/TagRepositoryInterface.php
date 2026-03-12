<?php

namespace App\Repositories\Interfaces;

use App\Models\TagModel;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface TagRepositoryInterface
{
    public function all(): Collection;

    public function paginate(array $filters): LengthAwarePaginator;

    public function find(int $id): ?TagModel;

    public function create(array $data): TagModel;

    public function delete(TagModel $tag): void;
}
