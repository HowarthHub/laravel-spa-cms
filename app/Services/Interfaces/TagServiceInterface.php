<?php

namespace App\Services\Interfaces;

use App\Models\TagModel;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface TagServiceInterface
{
    public function getAll(): Collection;

    public function getPaginatedList(array $filters): LengthAwarePaginator;

    public function create(array $data): TagModel;

    public function update(TagModel $tag, array $data): TagModel;

    public function delete(TagModel $tag): void;
}
