<?php

namespace App\Repositories\Interfaces;

use App\Models\MediaItemModel;
use Illuminate\Pagination\LengthAwarePaginator;

interface MediaRepositoryInterface
{
    public function paginate(array $filters): LengthAwarePaginator;

    public function find(int $id): ?MediaItemModel;

    public function create(array $data): MediaItemModel;

    public function update(MediaItemModel $item, array $data): MediaItemModel;

    public function delete(MediaItemModel $item): void;
}
