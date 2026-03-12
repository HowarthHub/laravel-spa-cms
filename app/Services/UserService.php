<?php

namespace App\Services;

use App\Models\UserModel;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Services\Interfaces\UserServiceInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class UserService implements UserServiceInterface
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository
    ) {}

    public function getPaginatedList(array $filters): LengthAwarePaginator
    {
        return $this->userRepository->paginateWithFilters($filters);
    }

    public function create(array $data): UserModel
    {
        $data['password'] = bcrypt($data['password']);

        $user = $this->userRepository->create($data);

        if (isset($data['role'])) {
            $user->assignRole($data['role']);
        }

        return $user;
    }

    public function update(UserModel $user, array $data): UserModel
    {
        if (! empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }

        $user = $this->userRepository->update($user, $data);

        if (isset($data['role'])) {
            $user->syncRoles([$data['role']]);
        }

        return $user;
    }

    public function delete(UserModel $user): void
    {
        $this->userRepository->delete($user);
    }
}
