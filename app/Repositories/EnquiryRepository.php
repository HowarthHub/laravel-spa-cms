<?php

namespace App\Repositories;

use App\Models\ContactEnquiryModel;
use App\Repositories\Interfaces\EnquiryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class EnquiryRepository implements EnquiryRepositoryInterface
{
    public function paginateWithFilters(array $filters): LengthAwarePaginator
    {
        return ContactEnquiryModel::query()
            ->when($filters['search'] ?? null, fn ($q, $s) => $q->where(fn ($q) => $q
                ->where('name', 'like', "%{$s}%")
                ->orWhere('email', 'like', "%{$s}%")
                ->orWhere('subject', 'like', "%{$s}%")
            ))
            ->when($filters['status'] ?? null, fn ($q, $s) => $q->where('status', $s))
            ->latest()
            ->paginate(config('cms.per_page.enquiries'));
    }

    public function find(int $id): ?ContactEnquiryModel
    {
        return ContactEnquiryModel::find($id);
    }

    public function create(array $data): ContactEnquiryModel
    {
        return ContactEnquiryModel::create($data);
    }

    public function update(ContactEnquiryModel $enquiry, array $data): ContactEnquiryModel
    {
        $enquiry->update($data);

        return $enquiry->fresh();
    }

    public function delete(ContactEnquiryModel $enquiry): void
    {
        $enquiry->delete();
    }

    public function bulkArchive(array $ids): void
    {
        ContactEnquiryModel::whereIn('id', $ids)->update(['status' => 'archived']);
    }

    public function countNew(): int
    {
        return ContactEnquiryModel::where('status', 'new')->count();
    }

    public function recent(int $limit): Collection
    {
        return ContactEnquiryModel::latest()->limit($limit)->get();
    }
}
