<?php

use App\Models\PageModel;

// --- Store with blocks ---

it('stores a page with blocks content', function () {
    actingAsSuperAdmin();

    $blocks = [
        ['id' => 'blk_abc123', 'type' => 'hero', 'data' => ['heading' => 'Welcome', 'subheading' => 'Hello world', 'backgroundImage' => '', 'ctaText' => '', 'ctaLink' => '']],
        ['id' => 'blk_def456', 'type' => 'richText', 'data' => ['content' => ['type' => 'doc', 'content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'Some text']]]]]]],
        ['id' => 'blk_ghi789', 'type' => 'spacer', 'data' => ['size' => 'large']],
    ];

    $this->post(route('admin.pages.store'), [
        'title' => 'Block Page',
        'status' => 'draft',
        'template' => 'page-builder',
        'content' => $blocks,
    ])->assertRedirect(route('admin.pages.index'));

    $page = PageModel::where('title', 'Block Page')->first();
    expect($page->content)->toBeArray()
        ->and($page->content)->toHaveCount(3)
        ->and($page->content[0]['type'])->toBe('hero')
        ->and($page->content[0]['data']['heading'])->toBe('Welcome');
});

// --- Round-trip ---

it('round-trips blocks content through store and edit', function () {
    actingAsSuperAdmin();

    $blocks = [
        ['id' => 'blk_test01', 'type' => 'callToAction', 'data' => ['heading' => 'Get Started', 'text' => 'Sign up now', 'buttonText' => 'Go', 'buttonLink' => '/signup', 'variant' => 'green']],
    ];

    $this->post(route('admin.pages.store'), [
        'title' => 'CTA Page',
        'status' => 'draft',
        'template' => 'page-builder',
        'content' => $blocks,
    ]);

    $page = PageModel::where('title', 'CTA Page')->first();

    $this->get(route('admin.pages.edit', $page))
        ->assertOk()
        ->assertInertia(fn ($p) => $p
            ->component('Admin/Pages/PageEditPage')
            ->where('page.content.0.type', 'callToAction')
            ->where('page.content.0.data.heading', 'Get Started')
            ->has('blockTypes')
        );
});

// --- Update from legacy to blocks ---

it('updates a page from legacy tiptap content to blocks format', function () {
    actingAsSuperAdmin();

    $page = PageModel::factory()->published()->create([
        'content' => ['type' => 'doc', 'content' => [['type' => 'paragraph']]],
    ]);

    $blocks = [
        ['id' => 'blk_new001', 'type' => 'richText', 'data' => ['content' => ['type' => 'doc', 'content' => [['type' => 'paragraph']]]]],
    ];

    $this->put(route('admin.pages.update', $page), [
        'title' => $page->title,
        'status' => 'published',
        'published_at' => now()->toDateTimeString(),
        'template' => 'page-builder',
        'content' => $blocks,
    ])->assertRedirect(route('admin.pages.index'));

    $page->refresh();
    expect($page->content_format)->toBe('blocks')
        ->and($page->content[0]['type'])->toBe('richText');
});

// --- Legacy content still works on public ---

it('renders legacy tiptap content on public pages', function () {
    $page = PageModel::factory()->published()->create([
        'slug' => 'legacy-page',
        'content' => ['type' => 'doc', 'content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'Legacy text']]]]],
    ]);

    $this->get(route('public.page.show', 'legacy-page'))
        ->assertOk()
        ->assertInertia(fn ($inertia) => $inertia
            ->component('Public/PageShowPage')
            ->where('page.content.type', 'doc')
        );
});

// --- Blocks content renders on public ---

it('renders blocks content on public pages', function () {
    $blocks = [
        ['id' => 'blk_pub001', 'type' => 'hero', 'data' => ['heading' => 'Public Hero', 'subheading' => '', 'backgroundImage' => '', 'ctaText' => '', 'ctaLink' => '']],
        ['id' => 'blk_pub002', 'type' => 'spacer', 'data' => ['size' => 'medium']],
    ];

    $page = PageModel::factory()->published()->create([
        'slug' => 'blocks-page',
        'template' => 'page-builder',
        'content' => $blocks,
    ]);

    $this->get(route('public.page.show', 'blocks-page'))
        ->assertOk()
        ->assertInertia(fn ($inertia) => $inertia
            ->component('Public/PageShowPage')
            ->where('page.content.0.type', 'hero')
            ->where('page.content.0.data.heading', 'Public Hero')
            ->where('page.content.1.type', 'spacer')
        );
});

// --- Content format accessor ---

it('detects content format correctly via accessor', function () {
    $blocksPage = PageModel::factory()->create([
        'content' => [
            ['id' => 'blk_001', 'type' => 'hero', 'data' => ['heading' => 'Test']],
        ],
    ]);

    $tiptapPage = PageModel::factory()->create([
        'content' => ['type' => 'doc', 'content' => []],
    ]);

    $emptyPage = PageModel::factory()->create(['content' => null]);

    expect($blocksPage->content_format)->toBe('blocks')
        ->and($tiptapPage->content_format)->toBe('tiptap')
        ->and($emptyPage->content_format)->toBe('tiptap');
});

// --- Create page passes blockTypes ---

it('passes blockTypes prop on create page', function () {
    actingAsSuperAdmin();

    $this->get(route('admin.pages.create'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Admin/Pages/PageCreatePage')
            ->has('blockTypes')
        );
});
