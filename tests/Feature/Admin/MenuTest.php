<?php

use App\Models\MenuModel;

// --- Index ---

it('lists menus for authorised user', function () {
    actingAsSuperAdmin();
    MenuModel::factory()->count(2)->create();

    $this->get(route('admin.menus.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Admin/Menus/MenuIndexPage')
            ->has('menus', 2)
        );
});

// --- Create ---

it('renders the create form', function () {
    actingAsSuperAdmin();

    $this->get(route('admin.menus.create'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('Admin/Menus/MenuCreatePage'));
});

// --- Store ---

it('creates a menu and redirects to edit', function () {
    actingAsSuperAdmin();

    $this->post(route('admin.menus.store'), [
        'name' => 'Main Navigation',
        'handle' => 'main-navigation',
    ])->assertRedirect();

    $this->assertDatabaseHas('menus', ['name' => 'Main Navigation', 'handle' => 'main-navigation']);
});

it('validates name and handle are required', function () {
    actingAsSuperAdmin();

    $this->post(route('admin.menus.store'), [])
        ->assertSessionHasErrors(['name', 'handle']);
});

// --- Edit ---

it('renders the edit form with tree structure', function () {
    actingAsSuperAdmin();
    $menu = MenuModel::factory()->create();

    $this->get(route('admin.menus.edit', $menu))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Admin/Menus/MenuEditPage')
            ->where('menu.id', $menu->id)
            ->has('tree')
            ->has('pages')
            ->has('posts')
        );
});

// --- Update ---

it('updates a menu name', function () {
    actingAsSuperAdmin();
    $menu = MenuModel::factory()->create();

    $this->put(route('admin.menus.update', $menu), [
        'name' => 'Updated Menu',
        'handle' => $menu->handle,
    ])->assertRedirect(route('admin.menus.edit', $menu));

    expect($menu->fresh()->name)->toBe('Updated Menu');
});

// --- Destroy ---

it('deletes a menu', function () {
    actingAsSuperAdmin();
    $menu = MenuModel::factory()->create();

    $this->delete(route('admin.menus.destroy', $menu))
        ->assertRedirect(route('admin.menus.index'));

    $this->assertDatabaseMissing('menus', ['id' => $menu->id]);
});

// --- Auth ---

it('denies access without manage menus permission', function () {
    actingAsEditor();

    $this->get(route('admin.menus.index'))
        ->assertForbidden();
});

it('redirects guests to login', function () {
    $this->get(route('admin.menus.index'))
        ->assertRedirect(route('login'));
});
