<?php

namespace App\Services;

use App\Repositories\Interfaces\MediaRepositoryInterface;
use App\Services\Interfaces\MediaServiceInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaService implements MediaServiceInterface
{
    public function __construct(
        private readonly MediaRepositoryInterface $mediaRepository
    ) {}

    public function getPaginatedList(array $filters): LengthAwarePaginator
    {
        return $this->mediaRepository->paginate($filters);
    }

    public function upload(UploadedFile $file): Media
    {
        // Standalone media uploads are handled via a dedicated "media" model
        // For now, return a placeholder — fully wired in Phase 8
        throw new \RuntimeException('Media upload not yet implemented.');
    }

    public function delete(Media $media): void
    {
        $this->mediaRepository->delete($media);
    }
}
