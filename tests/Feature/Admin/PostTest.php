<?php

use App\Models\CategoryModel;
use App\Models\PostModel;
use App\Models\TagModel;
use App\Models\UserModel;

// --- Index ---

it('lists posts for authorised user', function () {
    actingAsSuperAdmin();
    PostModel::factory()->count(3)->create();

    $this->get(route('admin.posts.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Admin/Posts/PostIndexPage')
            ->has('posts.data', 3)
        );
});

it('filters posts by status', function () {
    actingAsSuperAdmin();
    PostModel::factory()->published()->count(2)->create();
    PostModel::factory()->create(['status' => 'draft']);

    $this->get(route('admin.posts.index', ['status' => 'published']))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->has('posts.data', 2));
});

it('filters posts by search term', function () {
    actingAsSuperAdmin();
    PostModel::factory()->create(['title' => 'Unique Searchable Title']);
    PostModel::factory()->create(['title' => 'Other Post']);

    $this->get(route('admin.posts.index', ['search' => 'Unique Searchable']))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->has('posts.data', 1));
});

it('filters posts by category', function () {
    actingAsSuperAdmin();
    $category = CategoryModel::factory()->create();
    $post = PostModel::factory()->create();
    $post->categories()->attach($category);
    PostModel::factory()->create();

    $this->get(route('admin.posts.index', ['category_id' => $category->id]))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->has('posts.data', 1));
});

// --- Create ---

it('renders the create form with categories and tags', function () {
    actingAsSuperAdmin();
    CategoryModel::factory()->count(2)->create();
    TagModel::factory()->count(3)->create();

    $this->get(route('admin.posts.create'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Admin/Posts/PostCreatePage')
            ->has('categories', 2)
            ->has('tags', 3)
        );
});

// --- Store ---

it('creates a post and redirects', function () {
    $user = actingAsSuperAdmin();

    $this->post(route('admin.posts.store'), [
        'title' => 'My New Post',
        'status' => 'draft',
        'content' => [['type' => 'paragraph', 'content' => 'Hello world']],
    ])->assertRedirect(route('admin.posts.index'));

    $this->assertDatabaseHas('posts', [
        'title' => 'My New Post',
        'status' => 'draft',
        'author_id' => $user->id,
    ]);
});

it('syncs categories and tags when creating a post', function () {
    actingAsSuperAdmin();
    $categories = CategoryModel::factory()->count(2)->create();
    $tags = TagModel::factory()->count(2)->create();

    $this->post(route('admin.posts.store'), [
        'title' => 'Tagged Post',
        'status' => 'draft',
        'category_ids' => $categories->pluck('id')->toArray(),
        'tag_ids' => $tags->pluck('id')->toArray(),
    ]);

    $post = PostModel::where('title', 'Tagged Post')->first();
    expect($post->categories)->toHaveCount(2);
    expect($post->tags)->toHaveCount(2);
});

it('validates title is required on store', function () {
    actingAsSuperAdmin();

    $this->post(route('admin.posts.store'), [
        'status' => 'draft',
    ])->assertSessionHasErrors('title');
});

it('validates status is required on store', function () {
    actingAsSuperAdmin();

    $this->post(route('admin.posts.store'), [
        'title' => 'A Post',
    ])->assertSessionHasErrors('status');
});

it('validates status must be valid value', function () {
    actingAsSuperAdmin();

    $this->post(route('admin.posts.store'), [
        'title' => 'A Post',
        'status' => 'invalid',
    ])->assertSessionHasErrors('status');
});

it('validates published_at is required when status is scheduled', function () {
    actingAsSuperAdmin();

    $this->post(route('admin.posts.store'), [
        'title' => 'Scheduled Post',
        'status' => 'scheduled',
    ])->assertSessionHasErrors('published_at');
});

it('validates category_ids must exist', function () {
    actingAsSuperAdmin();

    $this->post(route('admin.posts.store'), [
        'title' => 'A Post',
        'status' => 'draft',
        'category_ids' => [9999],
    ])->assertSessionHasErrors('category_ids.0');
});

// --- Edit ---

it('renders the edit form with post data', function () {
    actingAsSuperAdmin();
    $post = PostModel::factory()->create();

    $this->get(route('admin.posts.edit', $post))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Admin/Posts/PostEditPage')
            ->where('post.id', $post->id)
            ->has('categories')
            ->has('tags')
        );
});

// --- Update ---

it('updates a post', function () {
    $user = actingAsSuperAdmin();
    $post = PostModel::factory()->create();

    $this->put(route('admin.posts.update', $post), [
        'title' => 'Updated Title',
        'status' => 'published',
        'published_at' => now()->toDateTimeString(),
    ])->assertRedirect(route('admin.posts.index'));

    expect($post->fresh())
        ->title->toBe('Updated Title')
        ->updated_by->toBe($user->id);
});

// --- Destroy ---

it('soft deletes a post', function () {
    actingAsSuperAdmin();
    $post = PostModel::factory()->create();

    $this->delete(route('admin.posts.destroy', $post))
        ->assertRedirect(route('admin.posts.index'));

    $this->assertSoftDeleted('posts', ['id' => $post->id]);
});

it('bulk soft deletes multiple posts', function () {
    actingAsSuperAdmin();
    $posts = PostModel::factory()->count(3)->create();

    $this->post(route('admin.posts.bulk-destroy'), [
        'ids' => $posts->pluck('id')->toArray(),
    ])->assertRedirect(route('admin.posts.index'));

    foreach ($posts as $post) {
        $this->assertSoftDeleted('posts', ['id' => $post->id]);
    }
});

// --- Auth ---

it('denies access to index without view posts permission', function () {
    actingAsViewer();

    // Viewer has view posts, so test with a user who has NO posts permissions
    $user = UserModel::factory()->active()->create();
    $this->actingAs($user);

    $this->get(route('admin.posts.index'))
        ->assertForbidden();
});

it('denies access to create without create posts permission', function () {
    actingAsViewer();

    $this->post(route('admin.posts.store'), [
        'title' => 'Unauthorized Post',
        'status' => 'draft',
    ])->assertForbidden();
});

it('denies access to delete without delete posts permission', function () {
    actingAsEditor();
    $post = PostModel::factory()->create();

    $this->delete(route('admin.posts.destroy', $post))
        ->assertForbidden();
});

it('redirects guests to login', function () {
    $this->get(route('admin.posts.index'))
        ->assertRedirect(route('login'));
});
