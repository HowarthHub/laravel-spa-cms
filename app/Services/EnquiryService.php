<?php

namespace App\Services;

use App\Models\ContactEnquiryModel;
use App\Repositories\Interfaces\EnquiryRepositoryInterface;
use App\Services\Interfaces\EnquiryServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class EnquiryService implements EnquiryServiceInterface
{
    public function __construct(
        private readonly EnquiryRepositoryInterface $enquiryRepository
    ) {}

    public function getPaginatedList(array $filters): LengthAwarePaginator
    {
        return $this->enquiryRepository->paginateWithFilters($filters);
    }

    public function find(int $id): ?ContactEnquiryModel
    {
        return $this->enquiryRepository->find($id);
    }

    public function markAsRead(ContactEnquiryModel $enquiry): ContactEnquiryModel
    {
        if ($enquiry->status === 'new') {
            return $this->enquiryRepository->update($enquiry, [
                'status' => 'read',
                'read_at' => now(),
            ]);
        }

        return $enquiry;
    }

    public function update(ContactEnquiryModel $enquiry, array $data): ContactEnquiryModel
    {
        return $this->enquiryRepository->update($enquiry, $data);
    }

    public function delete(ContactEnquiryModel $enquiry): void
    {
        $this->enquiryRepository->delete($enquiry);
    }

    public function bulkArchive(array $ids): void
    {
        $this->enquiryRepository->bulkArchive($ids);
    }

    public function countNew(): int
    {
        return $this->enquiryRepository->countNew();
    }

    public function recent(int $limit = 5): Collection
    {
        return $this->enquiryRepository->recent($limit);
    }
}
