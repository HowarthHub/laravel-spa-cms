<?php

use App\Models\UserModel;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;

it('renders the forgot password page', function () {
    $this->get(route('password.request'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('Auth/ForgotPasswordPage'));
});

it('sends a reset link for a valid email', function () {
    Notification::fake();

    $user = UserModel::factory()->active()->create();

    $this->post(route('password.email'), ['email' => $user->email])
        ->assertSessionHasNoErrors();

    Notification::assertSentTo($user, ResetPassword::class);
});

it('does not reveal whether an email exists', function () {
    Notification::fake();

    $this->post(route('password.email'), ['email' => 'nonexistent@example.com'])
        ->assertSessionHasErrors('email');

    Notification::assertNothingSent();
});

it('resets password with a valid token', function () {
    $user = UserModel::factory()->active()->create();

    $token = Password::createToken($user);

    $this->post(route('password.store'), [
        'token' => $token,
        'email' => $user->email,
        'password' => 'new-password-123',
        'password_confirmation' => 'new-password-123',
    ])->assertRedirect(route('login'));
});
