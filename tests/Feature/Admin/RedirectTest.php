<?php

use App\Models\RedirectModel;

// --- Index ---

it('lists redirects for authorised user', function () {
    actingAsSuperAdmin();
    RedirectModel::create(['source_path' => '/old-1', 'destination_path' => '/new-1', 'status_code' => 301]);
    RedirectModel::create(['source_path' => '/old-2', 'destination_path' => '/new-2', 'status_code' => 302]);

    $this->get(route('admin.redirects.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Admin/Redirects/RedirectIndexPage')
            ->has('redirects.data', 2)
        );
});

it('filters redirects by search term', function () {
    actingAsSuperAdmin();
    RedirectModel::create(['source_path' => '/about-us', 'destination_path' => '/about', 'status_code' => 301]);
    RedirectModel::create(['source_path' => '/contact', 'destination_path' => '/get-in-touch', 'status_code' => 301]);

    $this->get(route('admin.redirects.index', ['search' => 'about']))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->has('redirects.data', 1));
});

// --- Store ---

it('creates a redirect', function () {
    actingAsSuperAdmin();

    $this->post(route('admin.redirects.store'), [
        'source_path' => '/old-page',
        'destination_path' => '/new-page',
        'status_code' => 301,
        'is_active' => true,
    ])->assertRedirect(route('admin.redirects.index'));

    $this->assertDatabaseHas('redirects', [
        'source_path' => '/old-page',
        'destination_path' => '/new-page',
        'status_code' => 301,
    ]);
});

it('validates source_path is required', function () {
    actingAsSuperAdmin();

    $this->post(route('admin.redirects.store'), [
        'destination_path' => '/new-page',
        'status_code' => 301,
    ])->assertSessionHasErrors('source_path');
});

it('validates source_path starts with slash', function () {
    actingAsSuperAdmin();

    $this->post(route('admin.redirects.store'), [
        'source_path' => 'no-slash',
        'destination_path' => '/new-page',
        'status_code' => 301,
    ])->assertSessionHasErrors('source_path');
});

it('validates status_code is 301 or 302', function () {
    actingAsSuperAdmin();

    $this->post(route('admin.redirects.store'), [
        'source_path' => '/old',
        'destination_path' => '/new',
        'status_code' => 404,
    ])->assertSessionHasErrors('status_code');
});

it('validates source_path is unique', function () {
    actingAsSuperAdmin();
    RedirectModel::create(['source_path' => '/taken', 'destination_path' => '/dest', 'status_code' => 301]);

    $this->post(route('admin.redirects.store'), [
        'source_path' => '/taken',
        'destination_path' => '/other',
        'status_code' => 301,
    ])->assertSessionHasErrors('source_path');
});

// --- Update ---

it('updates a redirect', function () {
    actingAsSuperAdmin();
    $redirect = RedirectModel::create(['source_path' => '/old', 'destination_path' => '/new', 'status_code' => 301]);

    $this->put(route('admin.redirects.update', $redirect), [
        'source_path' => '/updated-old',
        'destination_path' => '/updated-new',
        'status_code' => 302,
        'is_active' => false,
    ])->assertRedirect(route('admin.redirects.index'));

    expect($redirect->fresh())
        ->source_path->toBe('/updated-old')
        ->destination_path->toBe('/updated-new')
        ->status_code->toBe(302)
        ->is_active->toBeFalse();
});

// --- Destroy ---

it('deletes a redirect', function () {
    actingAsSuperAdmin();
    $redirect = RedirectModel::create(['source_path' => '/old', 'destination_path' => '/new', 'status_code' => 301]);

    $this->delete(route('admin.redirects.destroy', $redirect))
        ->assertRedirect(route('admin.redirects.index'));

    $this->assertDatabaseMissing('redirects', ['id' => $redirect->id]);
});

// --- Auth ---

it('denies access without manage redirects permission', function () {
    actingAsViewer();

    $this->get(route('admin.redirects.index'))
        ->assertForbidden();
});

it('redirects guests to login', function () {
    $this->get(route('admin.redirects.index'))
        ->assertRedirect(route('login'));
});

// --- Middleware ---

it('performs a redirect when source path matches', function () {
    RedirectModel::create(['source_path' => '/old-page', 'destination_path' => '/new-page', 'status_code' => 301, 'is_active' => true]);

    $this->get('/old-page')
        ->assertRedirect('/new-page')
        ->assertStatus(301);
});

it('does not redirect when redirect is inactive', function () {
    RedirectModel::create(['source_path' => '/old-page', 'destination_path' => '/new-page', 'status_code' => 301, 'is_active' => false]);

    $this->get('/old-page')
        ->assertStatus(404);
});

it('increments hit count on redirect', function () {
    $redirect = RedirectModel::create(['source_path' => '/old-page', 'destination_path' => '/new-page', 'status_code' => 301, 'is_active' => true]);

    $this->get('/old-page');

    expect($redirect->fresh()->hit_count)->toBe(1);
});

it('does not redirect admin routes', function () {
    actingAsSuperAdmin();
    RedirectModel::create(['source_path' => '/admin/something', 'destination_path' => '/new', 'status_code' => 301, 'is_active' => true]);

    $this->get('/admin/something')
        ->assertStatus(404);
});

it('performs 302 redirect correctly', function () {
    RedirectModel::create(['source_path' => '/temp-page', 'destination_path' => '/other', 'status_code' => 302, 'is_active' => true]);

    $this->get('/temp-page')
        ->assertRedirect('/other')
        ->assertStatus(302);
});
