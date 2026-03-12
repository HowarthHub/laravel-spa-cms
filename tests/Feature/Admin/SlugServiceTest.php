<?php

use App\Models\PostModel;
use App\Services\SlugService;

beforeEach(function () {
    $this->slugService = new SlugService;
});

it('generates a slug from a title', function () {
    $slug = $this->slugService->generate('Hello World', PostModel::class);

    expect($slug)->toBe('hello-world');
});

it('appends a counter for duplicate slugs', function () {
    PostModel::factory()->create(['slug' => 'hello-world']);

    $slug = $this->slugService->generate('Hello World', PostModel::class);

    expect($slug)->toBe('hello-world-2');
});

it('increments counter for multiple duplicates', function () {
    PostModel::factory()->create(['slug' => 'hello-world']);
    PostModel::factory()->create(['slug' => 'hello-world-2']);

    $slug = $this->slugService->generate('Hello World', PostModel::class);

    expect($slug)->toBe('hello-world-3');
});

it('excludes given ID when checking uniqueness', function () {
    $post = PostModel::factory()->create(['slug' => 'hello-world']);

    $slug = $this->slugService->generate('Hello World', PostModel::class, $post->id);

    expect($slug)->toBe('hello-world');
});

it('handles special characters', function () {
    $slug = $this->slugService->generate('Héllo Wörld & Café!', PostModel::class);

    expect($slug)->toStartWith('hello-world');
});
