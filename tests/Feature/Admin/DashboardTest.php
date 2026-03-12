<?php

use App\Models\ContactEnquiryModel;
use App\Models\MediaItemModel;
use App\Models\PageModel;
use App\Models\PostModel;
use App\Models\UserModel;

it('shows the dashboard with stats for an authorised user', function () {
    actingAsSuperAdmin();

    $this->get(route('admin.dashboard'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Admin/Dashboard/DashboardIndexPage')
            ->has('stats')
            ->has('recentPosts')
            ->has('recentEnquiries')
            ->has('recentPages')
        );
});

it('returns correct stat counts', function () {
    actingAsSuperAdmin();

    PageModel::factory()->count(3)->create();
    PostModel::factory()->published()->count(2)->create();
    PostModel::factory()->create(['status' => 'draft']);
    ContactEnquiryModel::factory()->count(4)->create();
    MediaItemModel::factory()->count(5)->create();

    $this->get(route('admin.dashboard'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->where('stats.totalPages', 3)
            ->where('stats.publishedPosts', 2)
            ->where('stats.draftPosts', 1)
            ->where('stats.newEnquiries', 4)
            ->where('stats.totalMedia', 5)
        );
});

it('denies access without view dashboard permission', function () {
    $user = UserModel::factory()->active()->create();
    $this->actingAs($user);

    $this->get(route('admin.dashboard'))
        ->assertForbidden();
});

it('redirects guests to login', function () {
    $this->get(route('admin.dashboard'))
        ->assertRedirect(route('login'));
});
