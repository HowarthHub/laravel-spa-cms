<?php

namespace App\Repositories\Interfaces;

use App\Models\ServiceModel;
use Illuminate\Pagination\LengthAwarePaginator;

interface ServiceRepositoryInterface
{
    public function paginateWithFilters(array $filters): LengthAwarePaginator;

    public function find(int $id): ?ServiceModel;

    public function create(array $data): ServiceModel;

    public function update(ServiceModel $service, array $data): ServiceModel;

    public function delete(ServiceModel $service): void;

    public function bulkDelete(array $ids): void;
}
