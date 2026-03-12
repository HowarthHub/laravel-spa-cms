<?php

use App\Models\CategoryModel;

// --- Index ---

it('lists categories for authorised user', function () {
    actingAsSuperAdmin();
    CategoryModel::factory()->count(3)->create();

    $this->get(route('admin.categories.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Admin/Categories/CategoryIndexPage')
            ->has('categories.data', 3)
        );
});

it('filters categories by search term', function () {
    actingAsSuperAdmin();
    CategoryModel::factory()->create(['name' => 'Technology']);
    CategoryModel::factory()->create(['name' => 'Health']);

    $this->get(route('admin.categories.index', ['search' => 'Technology']))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->has('categories.data', 1));
});

// --- Create ---

it('renders the create form', function () {
    actingAsSuperAdmin();

    $this->get(route('admin.categories.create'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('Admin/Categories/CategoryCreatePage'));
});

// --- Store ---

it('creates a category', function () {
    actingAsSuperAdmin();

    $this->post(route('admin.categories.store'), [
        'name' => 'New Category',
    ])->assertRedirect(route('admin.categories.index'));

    $this->assertDatabaseHas('categories', ['name' => 'New Category']);
});

it('creates a category with a parent', function () {
    actingAsSuperAdmin();
    $parent = CategoryModel::factory()->create();

    $this->post(route('admin.categories.store'), [
        'name' => 'Child Category',
        'parent_id' => $parent->id,
    ]);

    $child = CategoryModel::where('name', 'Child Category')->first();
    expect($child->parent_id)->toBe($parent->id);
});

it('validates name is required', function () {
    actingAsSuperAdmin();

    $this->post(route('admin.categories.store'), [])
        ->assertSessionHasErrors('name');
});

// --- Edit ---

it('renders the edit form', function () {
    actingAsSuperAdmin();
    $category = CategoryModel::factory()->create();

    $this->get(route('admin.categories.edit', $category))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Admin/Categories/CategoryEditPage')
            ->where('category.id', $category->id)
        );
});

// --- Update ---

it('updates a category', function () {
    actingAsSuperAdmin();
    $category = CategoryModel::factory()->create();

    $this->put(route('admin.categories.update', $category), [
        'name' => 'Updated Category',
    ])->assertRedirect(route('admin.categories.index'));

    expect($category->fresh()->name)->toBe('Updated Category');
});

// --- Destroy ---

it('deletes a category', function () {
    actingAsSuperAdmin();
    $category = CategoryModel::factory()->create();

    $this->delete(route('admin.categories.destroy', $category))
        ->assertRedirect(route('admin.categories.index'));

    $this->assertDatabaseMissing('categories', ['id' => $category->id]);
});

// --- Auth ---

it('denies access without manage categories permission', function () {
    actingAsViewer();

    $this->get(route('admin.categories.index'))
        ->assertForbidden();
});

it('redirects guests to login', function () {
    $this->get(route('admin.categories.index'))
        ->assertRedirect(route('login'));
});
