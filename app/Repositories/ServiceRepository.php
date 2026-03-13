<?php

namespace App\Repositories;

use App\Models\ServiceModel;
use App\Repositories\Interfaces\ServiceRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class ServiceRepository implements ServiceRepositoryInterface
{
    public function paginateWithFilters(array $filters): LengthAwarePaginator
    {
        return ServiceModel::query()
            ->with(['author', 'editor'])
            ->when($filters['search'] ?? null, fn ($q, $s) => $q->where('title', 'like', "%{$s}%"))
            ->when($filters['status'] ?? null, fn ($q, $s) => $q->where('status', $s))
            ->orderBy('sort_order', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate(config('cms.per_page.services', 20));
    }

    public function find(int $id): ?ServiceModel
    {
        return ServiceModel::find($id);
    }

    public function create(array $data): ServiceModel
    {
        return ServiceModel::create($data);
    }

    public function update(ServiceModel $service, array $data): ServiceModel
    {
        $service->update($data);

        return $service->fresh();
    }

    public function delete(ServiceModel $service): void
    {
        $service->delete();
    }

    public function bulkDelete(array $ids): void
    {
        ServiceModel::whereIn('id', $ids)->delete();
    }

    public function bulkUpdateStatus(array $ids, string $status, ?string $publishedAt = null): void
    {
        $data = ['status' => $status];

        if ($publishedAt !== null) {
            $data['published_at'] = $publishedAt;
        }

        ServiceModel::whereIn('id', $ids)->update($data);
    }
}
