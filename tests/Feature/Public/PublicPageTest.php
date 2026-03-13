<?php

use App\Models\FormModel;
use App\Models\PageModel;
use App\Models\PostModel;
use App\Models\ServiceModel;
use App\Models\SettingModel;

it('renders homepage with published page when homepage is set', function () {
    $page = PageModel::factory()->published()->create(['title' => 'Welcome Home']);
    SettingModel::where('group', 'general')->where('key', 'homepage')->update(['value' => $page->id]);

    $this->get(route('public.home'))
        ->assertOk()
        ->assertInertia(fn ($inertia) => $inertia
            ->component('Public/PageShowPage')
            ->where('page.title', 'Welcome Home')
        );
});

it('renders homepage fallback when no homepage is set', function () {
    $this->get(route('public.home'))
        ->assertOk()
        ->assertInertia(fn ($inertia) => $inertia
            ->component('Public/HomePage')
            ->where('page', null)
        );
});

it('renders a published page by slug', function () {
    $page = PageModel::factory()->published()->create(['slug' => 'about-us', 'title' => 'About Us']);

    $this->get(route('public.page.show', 'about-us'))
        ->assertOk()
        ->assertInertia(fn ($inertia) => $inertia
            ->component('Public/PageShowPage')
            ->where('page.title', 'About Us')
        );
});

it('returns 404 for draft page', function () {
    PageModel::factory()->create(['slug' => 'draft-page', 'status' => 'draft']);

    $this->get(route('public.page.show', 'draft-page'))
        ->assertNotFound();
});

it('returns 404 for missing page slug', function () {
    $this->get(route('public.page.show', 'nonexistent'))
        ->assertNotFound();
});

it('includes posts when page uses blog template', function () {
    $page = PageModel::factory()->published()->create(['template' => 'blog']);
    PostModel::factory()->published()->count(3)->create();
    PostModel::factory()->create(['status' => 'draft']);
    SettingModel::where('group', 'general')->where('key', 'homepage')->update(['value' => $page->id]);

    $this->get(route('public.home'))
        ->assertOk()
        ->assertInertia(fn ($inertia) => $inertia
            ->has('posts.data', 3)
        );
});

it('includes services when page uses services template', function () {
    $page = PageModel::factory()->published()->create(['template' => 'services']);
    ServiceModel::factory()->published()->count(2)->create();
    ServiceModel::factory()->create(['status' => 'draft']);
    SettingModel::where('group', 'general')->where('key', 'homepage')->update(['value' => $page->id]);

    $this->get(route('public.home'))
        ->assertOk()
        ->assertInertia(fn ($inertia) => $inertia
            ->has('services', 2)
        );
});

it('includes form when page uses contact template', function () {
    $form = FormModel::factory()->create();
    $page = PageModel::factory()->published()->create([
        'template' => 'contact',
        'form_id' => $form->id,
    ]);
    SettingModel::where('group', 'general')->where('key', 'homepage')->update(['value' => $page->id]);

    $this->get(route('public.home'))
        ->assertOk()
        ->assertInertia(fn ($inertia) => $inertia
            ->has('form')
            ->where('form.id', $form->id)
        );
});

it('includes seo meta props', function () {
    $page = PageModel::factory()->published()->create([
        'slug' => 'test-page',
        'meta_title' => 'Custom SEO Title',
        'meta_description' => 'Custom description',
    ]);

    $this->get(route('public.page.show', 'test-page'))
        ->assertOk()
        ->assertInertia(fn ($inertia) => $inertia
            ->has('meta.title')
            ->has('meta.description')
            ->has('meta.canonicalUrl')
        );
});
