<?php

use App\Models\UserModel;

// --- Index ---

it('lists users for authorised user', function () {
    actingAsSuperAdmin();

    $this->get(route('admin.users.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Admin/Users/UserIndexPage')
            ->has('users.data')
            ->has('roles')
        );
});

it('filters users by search term', function () {
    actingAsSuperAdmin();
    UserModel::factory()->active()->create(['name' => 'Alice Wonderland']);
    UserModel::factory()->active()->create(['name' => 'Bob Builder']);

    $this->get(route('admin.users.index', ['search' => 'Alice']))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->has('users.data', 1));
});

it('filters users by role', function () {
    $admin = actingAsSuperAdmin();
    $editor = UserModel::factory()->active()->create();
    $editor->assignRole('editor');

    $this->get(route('admin.users.index', ['role' => 'editor']))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->has('users.data', 1));
});

// --- Create ---

it('renders the create form with roles', function () {
    actingAsSuperAdmin();

    $this->get(route('admin.users.create'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Admin/Users/UserCreatePage')
            ->has('roles')
        );
});

// --- Store ---

it('creates a user with a role', function () {
    actingAsSuperAdmin();

    $this->post(route('admin.users.store'), [
        'name' => 'New User',
        'email' => 'newuser@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'role' => 'editor',
        'is_active' => true,
    ])->assertRedirect(route('admin.users.index'));

    $user = UserModel::where('email', 'newuser@example.com')->first();
    expect($user)->not->toBeNull();
    expect($user->hasRole('editor'))->toBeTrue();
});

it('validates name is required', function () {
    actingAsSuperAdmin();

    $this->post(route('admin.users.store'), [
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'role' => 'editor',
    ])->assertSessionHasErrors('name');
});

it('validates email is unique', function () {
    actingAsSuperAdmin();
    $existing = UserModel::factory()->create();

    $this->post(route('admin.users.store'), [
        'name' => 'Duplicate',
        'email' => $existing->email,
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'role' => 'editor',
    ])->assertSessionHasErrors('email');
});

it('validates password confirmation', function () {
    actingAsSuperAdmin();

    $this->post(route('admin.users.store'), [
        'name' => 'Test',
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'different',
        'role' => 'editor',
    ])->assertSessionHasErrors('password');
});

// --- Edit ---

it('renders the edit form with user and roles', function () {
    actingAsSuperAdmin();
    $user = UserModel::factory()->active()->create();
    $user->assignRole('editor');

    $this->get(route('admin.users.edit', $user))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Admin/Users/UserEditPage')
            ->where('user.id', $user->id)
            ->has('roles')
        );
});

// --- Update ---

it('updates a user', function () {
    actingAsSuperAdmin();
    $user = UserModel::factory()->active()->create();
    $user->assignRole('editor');

    $this->put(route('admin.users.update', $user), [
        'name' => 'Updated Name',
        'email' => $user->email,
        'role' => 'admin',
        'is_active' => true,
    ])->assertRedirect(route('admin.users.index'));

    expect($user->fresh()->name)->toBe('Updated Name');
    expect($user->fresh()->hasRole('admin'))->toBeTrue();
});

// --- Destroy ---

it('deletes a user', function () {
    actingAsSuperAdmin();
    $user = UserModel::factory()->active()->create();

    $this->delete(route('admin.users.destroy', $user))
        ->assertRedirect(route('admin.users.index'));

    $this->assertDatabaseMissing('users', ['id' => $user->id]);
});

it('prevents self-deletion', function () {
    $user = actingAsSuperAdmin();

    $this->delete(route('admin.users.destroy', $user))
        ->assertRedirect(route('admin.users.index'))
        ->assertSessionHas('error', 'You cannot delete yourself.');

    $this->assertDatabaseHas('users', ['id' => $user->id]);
});

// --- Auth ---

it('denies access without manage users permission', function () {
    actingAsAdmin();

    $this->get(route('admin.users.index'))
        ->assertForbidden();
});

it('redirects guests to login', function () {
    $this->get(route('admin.users.index'))
        ->assertRedirect(route('login'));
});
