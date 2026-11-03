<?php

use App\Models\FormModel;
use App\Models\FormSubmissionModel;

// --- Index ---

it('lists forms for authorised user', function () {
    actingAsSuperAdmin();
    FormModel::factory()->count(3)->create();

    $this->get(route('admin.forms.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Admin/Forms/FormIndexPage')
            ->has('forms.data', 3)
        );
});

// --- Create ---

it('renders the create form', function () {
    actingAsSuperAdmin();

    $this->get(route('admin.forms.create'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('Admin/Forms/FormCreatePage'));
});

// --- Store ---

it('creates a form and redirects', function () {
    actingAsSuperAdmin();

    $this->post(route('admin.forms.store'), [
        'name' => 'Contact Form',
        'handle' => 'contact-form',
        'fields' => [
            ['label' => 'Name', 'type' => 'text', 'required' => true, 'placeholder' => ''],
            ['label' => 'Email', 'type' => 'email', 'required' => true, 'placeholder' => ''],
        ],
        'is_active' => true,
    ])->assertRedirect(route('admin.forms.index'));

    $this->assertDatabaseHas('forms', ['name' => 'Contact Form', 'handle' => 'contact-form']);
});

it('validates name is required on store', function () {
    actingAsSuperAdmin();

    $this->post(route('admin.forms.store'), [
        'fields' => [['label' => 'Name', 'type' => 'text', 'required' => true, 'placeholder' => '']],
    ])->assertSessionHasErrors('name');
});

it('validates fields are required on store', function () {
    actingAsSuperAdmin();

    $this->post(route('admin.forms.store'), [
        'name' => 'Test Form',
        'handle' => 'test-form',
    ])->assertSessionHasErrors('fields');
});

it('validates nested field structure', function () {
    actingAsSuperAdmin();

    $this->post(route('admin.forms.store'), [
        'name' => 'Bad Form',
        'handle' => 'bad-form',
        'fields' => [['invalid' => 'structure']],
    ])->assertSessionHasErrors();
});

// --- Edit ---

it('renders the edit form with submission count', function () {
    actingAsSuperAdmin();
    $form = FormModel::factory()->create();
    FormSubmissionModel::factory()->count(5)->create(['form_id' => $form->id]);

    $this->get(route('admin.forms.edit', $form))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Admin/Forms/FormEditPage')
            ->where('form.id', $form->id)
            ->where('form.submissions_count', 5)
        );
});

// --- Update ---

it('updates a form', function () {
    actingAsSuperAdmin();
    $form = FormModel::factory()->create();

    $this->put(route('admin.forms.update', $form), [
        'name' => 'Updated Form',
        'handle' => $form->handle,
        'fields' => $form->fields,
        'is_active' => false,
    ])->assertRedirect(route('admin.forms.index'));

    expect($form->fresh())
        ->name->toBe('Updated Form')
        ->is_active->toBeFalse();
});

// --- Destroy ---

it('deletes a form', function () {
    actingAsSuperAdmin();
    $form = FormModel::factory()->create();

    $this->delete(route('admin.forms.destroy', $form))
        ->assertRedirect(route('admin.forms.index'));

    $this->assertDatabaseMissing('forms', ['id' => $form->id]);
});

// --- Submissions ---

it('lists submissions for a form', function () {
    actingAsSuperAdmin();
    $form = FormModel::factory()->create();
    FormSubmissionModel::factory()->count(3)->create(['form_id' => $form->id]);

    $this->get(route('admin.forms.submissions', $form))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Admin/Forms/FormSubmissionsPage')
            ->has('submissions.data', 3)
        );
});

it('deletes a form submission', function () {
    actingAsSuperAdmin();
    $submission = FormSubmissionModel::factory()->create();

    $this->delete(route('admin.forms.submissions.destroy', $submission))
        ->assertRedirect(route('admin.forms.submissions', $submission->form_id));

    $this->assertDatabaseMissing('form_submissions', ['id' => $submission->id]);
});

// --- Auth ---

it('denies access without manage forms permission', function () {
    actingAsViewer();

    $this->get(route('admin.forms.index'))
        ->assertForbidden();
});

it('redirects guests to login', function () {
    $this->get(route('admin.forms.index'))
        ->assertRedirect(route('login'));
});
