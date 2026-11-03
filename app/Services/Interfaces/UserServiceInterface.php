<?php

namespace App\Services\Interfaces;

use App\Models\UserModel;
use Illuminate\Pagination\LengthAwarePaginator;

interface UserServiceInterface
{
    public function getPaginatedList(array $filters): LengthAwarePaginator;

    public function create(array $data): UserModel;

    public function update(UserModel $user, array $data): UserModel;

    public function delete(UserModel $user): void;
}
