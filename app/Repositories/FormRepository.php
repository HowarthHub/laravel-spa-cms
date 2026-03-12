<?php

namespace App\Repositories;

use App\Models\FormModel;
use App\Repositories\Interfaces\FormRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class FormRepository implements FormRepositoryInterface
{
    public function paginateWithFilters(array $filters): LengthAwarePaginator
    {
        return FormModel::query()
            ->withCount('submissions')
            ->when($filters['search'] ?? null, fn ($q, $s) => $q->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                    ->orWhere('handle', 'like', "%{$s}%");
            }))
            ->when(isset($filters['is_active']), fn ($q) => $q->where('is_active', $filters['is_active']))
            ->latest('updated_at')
            ->paginate(config('cms.per_page.forms', 20));
    }

    public function find(int $id): ?FormModel
    {
        return FormModel::find($id);
    }

    public function create(array $data): FormModel
    {
        return FormModel::create($data);
    }

    public function update(FormModel $form, array $data): FormModel
    {
        $form->update($data);

        return $form->fresh();
    }

    public function delete(FormModel $form): void
    {
        $form->delete();
    }
}
