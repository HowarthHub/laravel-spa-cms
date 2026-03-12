<?php

use App\Models\TagModel;

// --- Index ---

it('lists tags for authorised user', function () {
    actingAsSuperAdmin();
    TagModel::factory()->count(3)->create();

    $this->get(route('admin.tags.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Admin/Tags/TagIndexPage')
            ->has('tags.data', 3)
        );
});

it('filters tags by search term', function () {
    actingAsSuperAdmin();
    TagModel::factory()->create(['name' => 'Laravel']);
    TagModel::factory()->create(['name' => 'Vue']);

    $this->get(route('admin.tags.index', ['search' => 'Laravel']))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->has('tags.data', 1));
});

// --- Create ---

it('renders the create form', function () {
    actingAsSuperAdmin();

    $this->get(route('admin.tags.create'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('Admin/Tags/TagCreatePage'));
});

// --- Store ---

it('creates a tag', function () {
    actingAsSuperAdmin();

    $this->post(route('admin.tags.store'), [
        'name' => 'New Tag',
    ])->assertRedirect(route('admin.tags.index'));

    $this->assertDatabaseHas('tags', ['name' => 'New Tag']);
});

it('validates name is required', function () {
    actingAsSuperAdmin();

    $this->post(route('admin.tags.store'), [])
        ->assertSessionHasErrors('name');
});

// --- Edit ---

it('renders the edit form', function () {
    actingAsSuperAdmin();
    $tag = TagModel::factory()->create();

    $this->get(route('admin.tags.edit', $tag))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Admin/Tags/TagEditPage')
            ->where('tag.id', $tag->id)
        );
});

// --- Update ---

it('updates a tag', function () {
    actingAsSuperAdmin();
    $tag = TagModel::factory()->create();

    $this->put(route('admin.tags.update', $tag), [
        'name' => 'Updated Tag',
    ])->assertRedirect(route('admin.tags.index'));

    expect($tag->fresh()->name)->toBe('Updated Tag');
});

// --- Destroy ---

it('deletes a tag', function () {
    actingAsSuperAdmin();
    $tag = TagModel::factory()->create();

    $this->delete(route('admin.tags.destroy', $tag))
        ->assertRedirect(route('admin.tags.index'));

    $this->assertDatabaseMissing('tags', ['id' => $tag->id]);
});

// --- Auth ---

it('denies access without manage tags permission', function () {
    actingAsViewer();

    $this->get(route('admin.tags.index'))
        ->assertForbidden();
});

it('redirects guests to login', function () {
    $this->get(route('admin.tags.index'))
        ->assertRedirect(route('login'));
});
