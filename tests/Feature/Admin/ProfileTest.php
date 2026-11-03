<?php

use App\Models\UserModel;

it('renders the profile edit page', function () {
    $user = actingAsSuperAdmin();

    $this->get(route('admin.profile.edit'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Admin/Profile/ProfileEditPage')
            ->where('user.id', $user->id)
        );
});

it('updates name and email', function () {
    $user = actingAsSuperAdmin();

    $this->put(route('admin.profile.update'), [
        'name' => 'New Name',
        'email' => 'newemail@example.com',
    ])->assertRedirect(route('admin.profile.edit'));

    expect($user->fresh())
        ->name->toBe('New Name')
        ->email->toBe('newemail@example.com');
});

it('validates email is unique excluding self', function () {
    $user = actingAsSuperAdmin();
    $other = UserModel::factory()->create();

    $this->put(route('admin.profile.update'), [
        'name' => $user->name,
        'email' => $other->email,
    ])->assertSessionHasErrors('email');
});

it('updates password with correct current password', function () {
    $user = actingAsSuperAdmin();

    $this->put(route('admin.profile.password'), [
        'current_password' => 'password',
        'password' => 'new-password-123',
        'password_confirmation' => 'new-password-123',
    ])->assertRedirect(route('admin.profile.edit'));
});

it('rejects password update with incorrect current password', function () {
    actingAsSuperAdmin();

    $this->put(route('admin.profile.password'), [
        'current_password' => 'wrong-password',
        'password' => 'new-password-123',
        'password_confirmation' => 'new-password-123',
    ])->assertSessionHasErrors('current_password');
});

it('toggles dark mode', function () {
    $user = actingAsSuperAdmin();
    $originalDarkMode = $user->dark_mode;

    $this->post(route('admin.profile.toggle-dark-mode'))
        ->assertRedirect();

    expect($user->fresh()->dark_mode)->toBe(! $originalDarkMode);
});

it('allows any authenticated user to access profile', function () {
    actingAsViewer();

    $this->get(route('admin.profile.edit'))
        ->assertOk();
});

it('redirects guests to login', function () {
    $this->get(route('admin.profile.edit'))
        ->assertRedirect(route('login'));
});
