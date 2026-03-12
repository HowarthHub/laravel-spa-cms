<?php

namespace App\Services\Interfaces;

use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

interface MediaServiceInterface
{
    public function getPaginatedList(array $filters): LengthAwarePaginator;

    public function upload(UploadedFile $file): Media;

    public function delete(Media $media): void;
}
