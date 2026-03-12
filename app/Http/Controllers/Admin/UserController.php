<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Users\UserDestroyRequest;
use App\Http\Requests\Admin\Users\UserIndexRequest;
use App\Http\Requests\Admin\Users\UserStoreRequest;
use App\Http\Requests\Admin\Users\UserUpdateRequest;
use App\Models\UserModel;
use App\Services\Interfaces\UserServiceInterface;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function __construct(
        private readonly UserServiceInterface $userService
    ) {}

    public function index(UserIndexRequest $request): Response
    {
        return Inertia::render('Admin/Users/UserIndexPage', [
            'users' => $this->userService->getPaginatedList($request->validated()),
            'filters' => $request->only(['search', 'role']),
            'roles' => Role::orderBy('name')->pluck('name'),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Users/UserCreatePage', [
            'roles' => Role::orderBy('name')->pluck('name'),
        ]);
    }

    public function store(UserStoreRequest $request): RedirectResponse
    {
        $this->userService->create($request->validated());

        return redirect()->route('admin.users.index')->with('success', 'User created.');
    }

    public function edit(UserModel $user): Response
    {
        return Inertia::render('Admin/Users/UserEditPage', [
            'user' => $user->load('roles'),
            'roles' => Role::orderBy('name')->pluck('name'),
        ]);
    }

    public function update(UserUpdateRequest $request, UserModel $user): RedirectResponse
    {
        $this->userService->update($user, $request->validated());

        return redirect()->route('admin.users.index')->with('success', 'User updated.');
    }

    public function destroy(UserDestroyRequest $request, UserModel $user): RedirectResponse
    {
        if ($user->id === $request->user()->id) {
            return redirect()->route('admin.users.index')->with('error', 'You cannot delete yourself.');
        }

        $this->userService->delete($user);

        return redirect()->route('admin.users.index')->with('success', 'User deleted.');
    }
}
