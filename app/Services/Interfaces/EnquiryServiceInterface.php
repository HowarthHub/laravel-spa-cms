<?php

namespace App\Services\Interfaces;

use App\Models\ContactEnquiryModel;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface EnquiryServiceInterface
{
    public function getPaginatedList(array $filters): LengthAwarePaginator;

    public function find(int $id): ?ContactEnquiryModel;

    public function markAsRead(ContactEnquiryModel $enquiry): ContactEnquiryModel;

    public function update(ContactEnquiryModel $enquiry, array $data): ContactEnquiryModel;

    public function delete(ContactEnquiryModel $enquiry): void;

    public function bulkArchive(array $ids): void;

    public function countNew(): int;

    public function recent(int $limit = 5): Collection;
}
