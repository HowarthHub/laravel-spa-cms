<?php

use App\Models\FormModel;
use App\Models\FormSubmissionModel;
use App\Models\UserModel;

it('lists all form submissions for authorised user', function () {
    actingAsSuperAdmin();
    $form = FormModel::factory()->create();
    FormSubmissionModel::factory()->for($form, 'form')->count(3)->create();

    $this->get(route('admin.enquiries.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Admin/Forms/AllSubmissionsPage')
            ->has('submissions.data', 3)
        );
});

it('redirects guests to login', function () {
    $this->get(route('admin.enquiries.index'))
        ->assertRedirect(route('login'));
});
