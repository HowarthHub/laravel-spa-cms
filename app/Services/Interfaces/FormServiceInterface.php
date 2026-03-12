<?php

namespace App\Services\Interfaces;

use App\Models\FormModel;
use Illuminate\Pagination\LengthAwarePaginator;

interface FormServiceInterface
{
    public function getPaginatedList(array $filters): LengthAwarePaginator;

    public function create(array $data): FormModel;

    public function update(FormModel $form, array $data): FormModel;

    public function delete(FormModel $form): void;
}
