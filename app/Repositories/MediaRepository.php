<?php

namespace App\Repositories;

use App\Repositories\Interfaces\MediaRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaRepository implements MediaRepositoryInterface
{
    public function paginate(array $filters): LengthAwarePaginator
    {
        return Media::query()
            ->latest()
            ->paginate(config('cms.per_page.media'));
    }

    public function find(int $id): ?Media
    {
        return Media::find($id);
    }

    public function delete(Media $media): void
    {
        $media->delete();
    }
}
