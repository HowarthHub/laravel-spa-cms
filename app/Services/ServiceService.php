<?php

namespace App\Services;

use App\Models\ServiceModel;
use App\Repositories\Interfaces\ServiceRepositoryInterface;
use App\Services\Interfaces\ServiceServiceInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class ServiceService implements ServiceServiceInterface
{
    public function __construct(
        private readonly ServiceRepositoryInterface $serviceRepository
    ) {}

    public function getPaginatedList(array $filters): LengthAwarePaginator
    {
        return $this->serviceRepository->paginateWithFilters($filters);
    }

    public function create(array $data): ServiceModel
    {
        $data['slug'] = !empty($data['slug']) ? $data['slug'] : Str::slug($data['title']);
        $data['author_id'] = auth()->id();

        return $this->serviceRepository->create($data);
    }

    public function update(ServiceModel $service, array $data): ServiceModel
    {
        $data['updated_by'] = auth()->id();

        return $this->serviceRepository->update($service, $data);
    }

    public function delete(ServiceModel $service): void
    {
        $this->serviceRepository->delete($service);
    }

    public function bulkDelete(array $ids): void
    {
        $this->serviceRepository->bulkDelete($ids);
    }

    public function bulkDraft(array $ids): void
    {
        $this->serviceRepository->bulkUpdateStatus($ids, 'draft');
    }

    public function bulkPublish(array $ids): void
    {
        $this->serviceRepository->bulkUpdateStatus($ids, 'published', now()->toDateTimeString());
    }
}
