<?php

use App\Models\FormModel;
use App\Models\PageModel;
use App\Models\UserModel;

// --- Index ---

it('lists pages for authorised user', function () {
    actingAsSuperAdmin();
    PageModel::factory()->count(3)->create();

    $this->get(route('admin.pages.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Admin/Pages/PageIndexPage')
            ->has('pages.data', 3)
        );
});

it('filters pages by status', function () {
    actingAsSuperAdmin();
    PageModel::factory()->published()->count(2)->create();
    PageModel::factory()->create(['status' => 'draft']);

    $this->get(route('admin.pages.index', ['status' => 'published']))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->has('pages.data', 2));
});

it('filters pages by search term', function () {
    actingAsSuperAdmin();
    PageModel::factory()->create(['title' => 'About Us Page']);
    PageModel::factory()->create(['title' => 'Contact Page']);

    $this->get(route('admin.pages.index', ['search' => 'About Us']))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->has('pages.data', 1));
});

// --- Create ---

it('renders the create form with templates, pages, and forms', function () {
    actingAsSuperAdmin();
    FormModel::factory()->create();

    $this->get(route('admin.pages.create'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Admin/Pages/PageCreatePage')
            ->has('templates')
            ->has('pages')
            ->has('forms')
        );
});

// --- Store ---

it('creates a page and redirects', function () {
    $user = actingAsSuperAdmin();

    $this->post(route('admin.pages.store'), [
        'title' => 'My New Page',
        'status' => 'draft',
        'template' => 'default',
    ])->assertRedirect(route('admin.pages.index'));

    $this->assertDatabaseHas('pages', [
        'title' => 'My New Page',
        'author_id' => $user->id,
    ]);
});

it('creates a page with a parent', function () {
    actingAsSuperAdmin();
    $parent = PageModel::factory()->create();

    $this->post(route('admin.pages.store'), [
        'title' => 'Child Page',
        'status' => 'draft',
        'parent_id' => $parent->id,
    ]);

    $child = PageModel::where('title', 'Child Page')->first();
    expect($child->parent_id)->toBe($parent->id);
});

it('renders the create page with active forms listed', function () {
    actingAsSuperAdmin();
    FormModel::factory()->create();
    FormModel::factory()->inactive()->create();

    $this->get(route('admin.pages.create'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->has('forms', 1));
});

it('validates title is required on store', function () {
    actingAsSuperAdmin();

    $this->post(route('admin.pages.store'), [
        'status' => 'draft',
    ])->assertSessionHasErrors('title');
});

it('validates status is required on store', function () {
    actingAsSuperAdmin();

    $this->post(route('admin.pages.store'), [
        'title' => 'A Page',
    ])->assertSessionHasErrors('status');
});

it('validates published_at is required when status is scheduled', function () {
    actingAsSuperAdmin();

    $this->post(route('admin.pages.store'), [
        'title' => 'Scheduled Page',
        'status' => 'scheduled',
    ])->assertSessionHasErrors('published_at');
});

// --- Edit ---

it('renders the edit form with page data', function () {
    actingAsSuperAdmin();
    $page = PageModel::factory()->create();

    $this->get(route('admin.pages.edit', $page))
        ->assertOk()
        ->assertInertia(fn ($p) => $p
            ->component('Admin/Pages/PageEditPage')
            ->where('page.id', $page->id)
            ->has('templates')
            ->has('forms')
        );
});

// --- Update ---

it('updates a page', function () {
    $user = actingAsSuperAdmin();
    $page = PageModel::factory()->create();

    $this->put(route('admin.pages.update', $page), [
        'title' => 'Updated Page Title',
        'status' => 'published',
        'published_at' => now()->toDateTimeString(),
    ])->assertRedirect(route('admin.pages.index'));

    expect($page->fresh())
        ->title->toBe('Updated Page Title')
        ->updated_by->toBe($user->id);
});

// --- Destroy ---

it('soft deletes a page', function () {
    actingAsSuperAdmin();
    $page = PageModel::factory()->create();

    $this->delete(route('admin.pages.destroy', $page))
        ->assertRedirect(route('admin.pages.index'));

    $this->assertSoftDeleted('pages', ['id' => $page->id]);
});

it('bulk soft deletes multiple pages', function () {
    actingAsSuperAdmin();
    $pages = PageModel::factory()->count(3)->create();

    $this->post(route('admin.pages.bulk-destroy'), [
        'ids' => $pages->pluck('id')->toArray(),
    ])->assertRedirect(route('admin.pages.index'));

    foreach ($pages as $page) {
        $this->assertSoftDeleted('pages', ['id' => $page->id]);
    }
});

// --- Auth ---

it('denies access without view pages permission', function () {
    $user = UserModel::factory()->active()->create();
    $this->actingAs($user);

    $this->get(route('admin.pages.index'))
        ->assertForbidden();
});

it('denies store without create pages permission', function () {
    actingAsViewer();

    $this->post(route('admin.pages.store'), [
        'title' => 'Unauthorized',
        'status' => 'draft',
    ])->assertForbidden();
});

it('redirects guests to login', function () {
    $this->get(route('admin.pages.index'))
        ->assertRedirect(route('login'));
});
