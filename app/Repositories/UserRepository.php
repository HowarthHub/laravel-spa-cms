<?php

namespace App\Repositories;

use App\Models\UserModel;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class UserRepository implements UserRepositoryInterface
{
    public function paginateWithFilters(array $filters): LengthAwarePaginator
    {
        return UserModel::query()
            ->latest()
            ->paginate(config('cms.per_page.users'));
    }

    public function find(int $id): ?UserModel
    {
        return UserModel::find($id);
    }

    public function create(array $data): UserModel
    {
        return UserModel::create($data);
    }

    public function update(UserModel $user, array $data): UserModel
    {
        $user->update($data);

        return $user->fresh();
    }

    public function delete(UserModel $user): void
    {
        $user->delete();
    }
}
