<?php

namespace App\Repositories\Interfaces;

use App\Models\FormModel;
use Illuminate\Pagination\LengthAwarePaginator;

interface FormRepositoryInterface
{
    public function paginateWithFilters(array $filters): LengthAwarePaginator;

    public function find(int $id): ?FormModel;

    public function create(array $data): FormModel;

    public function update(FormModel $form, array $data): FormModel;

    public function delete(FormModel $form): void;
}
