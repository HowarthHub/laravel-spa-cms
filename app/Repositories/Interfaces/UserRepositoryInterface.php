<?php

namespace App\Repositories\Interfaces;

use App\Models\UserModel;
use Illuminate\Pagination\LengthAwarePaginator;

interface UserRepositoryInterface
{
    public function paginateWithFilters(array $filters): LengthAwarePaginator;

    public function find(int $id): ?UserModel;

    public function create(array $data): UserModel;

    public function update(UserModel $user, array $data): UserModel;

    public function delete(UserModel $user): void;
}
