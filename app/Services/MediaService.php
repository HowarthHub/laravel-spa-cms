<?php

namespace App\Services;

use App\Models\MediaItemModel;
use App\Repositories\Interfaces\MediaRepositoryInterface;
use App\Services\Interfaces\MediaServiceInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;

class MediaService implements MediaServiceInterface
{
    public function __construct(
        private readonly MediaRepositoryInterface $mediaRepository
    ) {}

    public function getPaginatedList(array $filters): LengthAwarePaginator
    {
        return $this->mediaRepository->paginate($filters);
    }

    public function upload(UploadedFile $file, int $userId): MediaItemModel
    {
        $item = $this->mediaRepository->create([
            'title' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
            'uploaded_by' => $userId,
        ]);

        $item->addMedia($file)
            ->toMediaCollection('files');

        return $item->fresh('media');
    }

    public function update(MediaItemModel $item, array $data): MediaItemModel
    {
        return $this->mediaRepository->update($item, $data);
    }

    public function delete(MediaItemModel $item): void
    {
        $this->mediaRepository->delete($item);
    }
}
