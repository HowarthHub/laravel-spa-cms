<?php

use App\Models\ServiceModel;

// --- Index ---

it('lists services for authorised user', function () {
    actingAsSuperAdmin();
    ServiceModel::factory()->count(3)->create();

    $this->get(route('admin.services.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Admin/Services/ServiceIndexPage')
            ->has('services.data', 3)
        );
});

it('filters services by status', function () {
    actingAsSuperAdmin();
    ServiceModel::factory()->published()->count(2)->create();
    ServiceModel::factory()->create(['status' => 'draft']);

    $this->get(route('admin.services.index', ['status' => 'published']))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->has('services.data', 2));
});

it('filters services by search term', function () {
    actingAsSuperAdmin();
    ServiceModel::factory()->create(['title' => 'Web Development']);
    ServiceModel::factory()->create(['title' => 'SEO Consulting']);

    $this->get(route('admin.services.index', ['search' => 'Web Development']))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->has('services.data', 1));
});

// --- Create ---

it('renders the create form', function () {
    actingAsSuperAdmin();

    $this->get(route('admin.services.create'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('Admin/Services/ServiceCreatePage'));
});

// --- Store ---

it('creates a service and redirects', function () {
    $user = actingAsSuperAdmin();

    $this->post(route('admin.services.store'), [
        'title' => 'New Service',
        'status' => 'draft',
    ])->assertRedirect(route('admin.services.index'));

    $this->assertDatabaseHas('services', [
        'title' => 'New Service',
        'author_id' => $user->id,
    ]);
});

it('validates title is required on store', function () {
    actingAsSuperAdmin();

    $this->post(route('admin.services.store'), ['status' => 'draft'])
        ->assertSessionHasErrors('title');
});

// --- Edit ---

it('renders the edit form with service data', function () {
    actingAsSuperAdmin();
    $service = ServiceModel::factory()->create();

    $this->get(route('admin.services.edit', $service))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Admin/Services/ServiceEditPage')
            ->where('service.id', $service->id)
        );
});

// --- Update ---

it('updates a service', function () {
    actingAsSuperAdmin();
    $service = ServiceModel::factory()->create();

    $this->put(route('admin.services.update', $service), [
        'title' => 'Updated Service',
        'status' => 'published',
        'published_at' => now()->toDateTimeString(),
    ])->assertRedirect(route('admin.services.index'));

    expect($service->fresh()->title)->toBe('Updated Service');
});

// --- Destroy ---

it('soft deletes a service', function () {
    actingAsSuperAdmin();
    $service = ServiceModel::factory()->create();

    $this->delete(route('admin.services.destroy', $service))
        ->assertRedirect(route('admin.services.index'));

    $this->assertSoftDeleted('services', ['id' => $service->id]);
});

it('bulk soft deletes multiple services', function () {
    actingAsSuperAdmin();
    $services = ServiceModel::factory()->count(3)->create();

    $this->post(route('admin.services.bulk-destroy'), [
        'ids' => $services->pluck('id')->toArray(),
    ])->assertRedirect(route('admin.services.index'));

    foreach ($services as $service) {
        $this->assertSoftDeleted('services', ['id' => $service->id]);
    }
});

// --- Auth ---

it('denies access without manage services permission', function () {
    actingAsViewer();

    $this->get(route('admin.services.index'))
        ->assertForbidden();
});

it('redirects guests to login', function () {
    $this->get(route('admin.services.index'))
        ->assertRedirect(route('login'));
});
