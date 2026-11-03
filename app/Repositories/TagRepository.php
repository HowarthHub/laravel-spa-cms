<?php

namespace App\Repositories;

use App\Models\TagModel;
use App\Repositories\Interfaces\TagRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class TagRepository implements TagRepositoryInterface
{
    public function all(): Collection
    {
        return TagModel::orderBy('name')->get();
    }

    public function paginate(array $filters): LengthAwarePaginator
    {
        return TagModel::query()
            ->withCount('posts')
            ->when($filters['search'] ?? null, fn ($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->orderBy('name')
            ->paginate(config('cms.per_page.pages'));
    }

    public function find(int $id): ?TagModel
    {
        return TagModel::find($id);
    }

    public function create(array $data): TagModel
    {
        return TagModel::create($data);
    }

    public function update(TagModel $tag, array $data): TagModel
    {
        $tag->update($data);

        return $tag->fresh();
    }

    public function delete(TagModel $tag): void
    {
        $tag->delete();
    }
}
