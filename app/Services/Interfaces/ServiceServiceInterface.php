<?php

namespace App\Services\Interfaces;

use App\Models\ServiceModel;
use Illuminate\Pagination\LengthAwarePaginator;

interface ServiceServiceInterface
{
    public function getPaginatedList(array $filters): LengthAwarePaginator;

    public function create(array $data): ServiceModel;

    public function update(ServiceModel $service, array $data): ServiceModel;

    public function delete(ServiceModel $service): void;

    public function bulkDelete(array $ids): void;

    public function bulkDraft(array $ids): void;

    public function bulkPublish(array $ids): void;
}
