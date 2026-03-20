<?php

use App\Models\PageModel;
use App\Models\PostModel;
use App\Models\UserModel;

beforeEach(function () {
    $this->author = UserModel::factory()->create();
});

it('publishes scheduled posts when publish date has passed', function () {
    $post = PostModel::factory()->create([
        'status' => 'scheduled',
        'published_at' => now()->subMinute(),
        'author_id' => $this->author->id,
    ]);

    $this->artisan('content:publish-scheduled')
        ->expectsOutputToContain('Published 1 item(s)')
        ->assertSuccessful();

    expect($post->fresh()->status)->toBe('published');
});

it('publishes scheduled pages when publish date has passed', function () {
    $page = PageModel::factory()->create([
        'status' => 'scheduled',
        'published_at' => now()->subMinute(),
        'author_id' => $this->author->id,
    ]);

    $this->artisan('content:publish-scheduled')->assertSuccessful();

    expect($page->fresh()->status)->toBe('published');
});

it('does not publish items scheduled for the future', function () {
    $post = PostModel::factory()->create([
        'status' => 'scheduled',
        'published_at' => now()->addHour(),
        'author_id' => $this->author->id,
    ]);

    $this->artisan('content:publish-scheduled')
        ->expectsOutputToContain('Published 0 item(s)')
        ->assertSuccessful();

    expect($post->fresh()->status)->toBe('scheduled');
});

it('does not affect draft or already published items', function () {
    $draft = PostModel::factory()->create([
        'status' => 'draft',
        'published_at' => now()->subHour(),
        'author_id' => $this->author->id,
    ]);

    $published = PostModel::factory()->create([
        'status' => 'published',
        'published_at' => now()->subHour(),
        'author_id' => $this->author->id,
    ]);

    $this->artisan('content:publish-scheduled')->assertSuccessful();

    expect($draft->fresh()->status)->toBe('draft');
    expect($published->fresh()->status)->toBe('published');
});
