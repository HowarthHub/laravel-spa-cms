<?php

use App\Models\FormModel;
use App\Notifications\FormSubmissionNotification;
use Illuminate\Support\Facades\Notification;

it('submits a form successfully', function () {
    $form = FormModel::factory()->create();

    $this->post(route('public.form.submit', $form), [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'message' => 'Hello there',
    ])
        ->assertRedirect()
        ->assertSessionHas('success');

    $this->assertDatabaseHas('form_submissions', [
        'form_id' => $form->id,
    ]);
});

it('fails validation for required fields', function () {
    $form = FormModel::factory()->create();

    $this->post(route('public.form.submit', $form), [
        'name' => '',
        'email' => '',
    ])
        ->assertSessionHasErrors(['name', 'email']);
});

it('fails validation for invalid email', function () {
    $form = FormModel::factory()->create();

    $this->post(route('public.form.submit', $form), [
        'name' => 'John',
        'email' => 'not-an-email',
    ])
        ->assertSessionHasErrors('email');
});

it('sends email notification when notification_email is set', function () {
    Notification::fake();

    $form = FormModel::factory()->create([
        'notification_email' => 'admin@example.com',
    ]);

    $this->post(route('public.form.submit', $form), [
        'name' => 'Jane',
        'email' => 'jane@example.com',
    ]);

    Notification::assertSentOnDemand(FormSubmissionNotification::class);
});

it('does not send email when notification_email is empty', function () {
    Notification::fake();

    $form = FormModel::factory()->create([
        'notification_email' => null,
    ]);

    $this->post(route('public.form.submit', $form), [
        'name' => 'Jane',
        'email' => 'jane@example.com',
    ]);

    Notification::assertNothingSent();
});

it('redirects back with success flash after submission', function () {
    $form = FormModel::factory()->create([
        'success_message' => 'Thanks for reaching out!',
    ]);

    $this->post(route('public.form.submit', $form), [
        'name' => 'Jane',
        'email' => 'jane@example.com',
    ])
        ->assertRedirect()
        ->assertSessionHas('success', 'Thanks for reaching out!');
});
