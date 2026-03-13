<?php

use App\Models\ServiceModel;

it('lists published services in sort order', function () {
    ServiceModel::factory()->published()->create(['sort_order' => 2, 'title' => 'Second']);
    ServiceModel::factory()->published()->create(['sort_order' => 1, 'title' => 'First']);
    ServiceModel::factory()->create(['status' => 'draft']);

    $this->get(route('public.services.index'))
        ->assertOk()
        ->assertInertia(fn ($inertia) => $inertia
            ->component('Public/ServicesIndexPage')
            ->has('services', 2)
            ->where('services.0.title', 'First')
            ->where('services.1.title', 'Second')
        );
});

it('renders single service page', function () {
    ServiceModel::factory()->published()->create(['slug' => 'web-design', 'title' => 'Web Design']);

    $this->get(route('public.services.show', 'web-design'))
        ->assertOk()
        ->assertInertia(fn ($inertia) => $inertia
            ->component('Public/ServiceShowPage')
            ->where('service.slug', 'web-design')
        );
});

it('returns 404 for unpublished service', function () {
    ServiceModel::factory()->create(['slug' => 'draft-service', 'status' => 'draft']);

    $this->get(route('public.services.show', 'draft-service'))
        ->assertNotFound();
});

it('returns 404 for nonexistent service slug', function () {
    $this->get(route('public.services.show', 'no-such-service'))
        ->assertNotFound();
});

it('includes meta props on services index', function () {
    $this->get(route('public.services.index'))
        ->assertOk()
        ->assertInertia(fn ($inertia) => $inertia
            ->has('meta.title')
            ->has('meta.description')
        );
});
