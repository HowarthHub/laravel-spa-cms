<?php

use App\Models\CategoryModel;
use App\Models\PostModel;

it('lists only published posts on blog index', function () {
    PostModel::factory()->published()->count(3)->create();
    PostModel::factory()->create(['status' => 'draft']);
    PostModel::factory()->scheduled()->create();

    $this->get(route('public.blog.index'))
        ->assertOk()
        ->assertInertia(fn ($inertia) => $inertia
            ->component('Public/BlogIndexPage')
            ->has('posts.data', 3)
        );
});

it('paginates blog index', function () {
    PostModel::factory()->published()->count(15)->create();

    $this->get(route('public.blog.index'))
        ->assertOk()
        ->assertInertia(fn ($inertia) => $inertia
            ->has('posts.data', 12)
            ->where('posts.last_page', 2)
        );
});

it('renders single post with author and categories', function () {
    $post = PostModel::factory()->published()->create(['slug' => 'my-post']);
    $category = CategoryModel::factory()->create();
    $post->categories()->attach($category);

    $this->get(route('public.blog.show', 'my-post'))
        ->assertOk()
        ->assertInertia(fn ($inertia) => $inertia
            ->component('Public/BlogShowPage')
            ->where('post.slug', 'my-post')
            ->has('post.author')
            ->has('post.categories', 1)
        );
});

it('returns related posts for single post', function () {
    $category = CategoryModel::factory()->create();
    $post = PostModel::factory()->published()->create(['slug' => 'main-post']);
    $post->categories()->attach($category);

    $related = PostModel::factory()->published()->count(2)->create();
    foreach ($related as $r) {
        $r->categories()->attach($category);
    }

    $this->get(route('public.blog.show', 'main-post'))
        ->assertOk()
        ->assertInertia(fn ($inertia) => $inertia
            ->has('relatedPosts', 2)
        );
});

it('returns 404 for unpublished post', function () {
    PostModel::factory()->create(['slug' => 'draft-post', 'status' => 'draft']);

    $this->get(route('public.blog.show', 'draft-post'))
        ->assertNotFound();
});

it('returns 404 for nonexistent post slug', function () {
    $this->get(route('public.blog.show', 'no-such-post'))
        ->assertNotFound();
});
