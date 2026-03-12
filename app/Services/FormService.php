<?php

namespace App\Services;

use App\Models\FormModel;
use App\Repositories\Interfaces\FormRepositoryInterface;
use App\Services\Interfaces\FormServiceInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class FormService implements FormServiceInterface
{
    public function __construct(
        private readonly FormRepositoryInterface $formRepository
    ) {}

    public function getPaginatedList(array $filters): LengthAwarePaginator
    {
        return $this->formRepository->paginateWithFilters($filters);
    }

    public function create(array $data): FormModel
    {
        if (empty($data['handle'])) {
            $data['handle'] = Str::slug($data['name']);
        }

        return $this->formRepository->create($data);
    }

    public function update(FormModel $form, array $data): FormModel
    {
        return $this->formRepository->update($form, $data);
    }

    public function delete(FormModel $form): void
    {
        $this->formRepository->delete($form);
    }
}
