<?php

use App\Models\UserModel;

it('renders the login page', function () {
    $this->get(route('login'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('Auth/LoginPage'));
});

it('authenticates a valid user and redirects to admin', function () {
    $user = UserModel::factory()->active()->create();

    $this->post(route('login'), [
        'email' => $user->email,
        'password' => 'password',
    ])->assertRedirect(route('admin.dashboard'));

    $this->assertAuthenticatedAs($user);
});

it('updates last_login_at on successful login', function () {
    $user = UserModel::factory()->active()->create(['last_login_at' => null]);

    $this->post(route('login'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    expect($user->fresh()->last_login_at)->not->toBeNull();
});

it('rejects invalid credentials', function () {
    $user = UserModel::factory()->active()->create();

    $this->post(route('login'), [
        'email' => $user->email,
        'password' => 'wrong-password',
    ])->assertSessionHasErrors('email');

    $this->assertGuest();
});

it('rejects an inactive user', function () {
    $user = UserModel::factory()->inactive()->create();

    $this->post(route('login'), [
        'email' => $user->email,
        'password' => 'password',
    ])->assertSessionHasErrors('email');

    $this->assertGuest();
});

it('throttles after 5 failed attempts', function () {
    $user = UserModel::factory()->active()->create();

    for ($i = 0; $i < 5; $i++) {
        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);
    }

    $this->post(route('login'), [
        'email' => $user->email,
        'password' => 'wrong-password',
    ])->assertSessionHasErrors('email');

    $errors = session('errors')->get('email');
    expect(collect($errors)->contains(fn ($msg) => str_contains($msg, 'seconds')))->toBeTrue();
});

it('logs the user out', function () {
    $user = UserModel::factory()->active()->create();

    $this->actingAs($user)
        ->post(route('logout'))
        ->assertRedirect(route('login'));

    $this->assertGuest();
});
