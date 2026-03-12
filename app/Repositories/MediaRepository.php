<?php

namespace App\Repositories;

use App\Models\MediaItemModel;
use App\Repositories\Interfaces\MediaRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class MediaRepository implements MediaRepositoryInterface
{
    public function paginate(array $filters): LengthAwarePaginator
    {
        return MediaItemModel::query()
            ->with('media')
            ->when($filters['search'] ?? null, fn ($q, $s) => $q->where('title', 'like', "%{$s}%"))
            ->latest()
            ->paginate(config('cms.per_page.media'));
    }

    public function find(int $id): ?MediaItemModel
    {
        return MediaItemModel::with('media')->find($id);
    }

    public function create(array $data): MediaItemModel
    {
        return MediaItemModel::create($data);
    }

    public function update(MediaItemModel $item, array $data): MediaItemModel
    {
        $item->update($data);

        return $item->fresh('media');
    }

    public function delete(MediaItemModel $item): void
    {
        $item->delete();
    }
}
