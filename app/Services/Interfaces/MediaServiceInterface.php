<?php

namespace App\Services\Interfaces;

use App\Models\MediaItemModel;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;

interface MediaServiceInterface
{
    public function getPaginatedList(array $filters): LengthAwarePaginator;

    public function upload(UploadedFile $file, int $userId): MediaItemModel;

    public function update(MediaItemModel $item, array $data): MediaItemModel;

    public function delete(MediaItemModel $item): void;
}
