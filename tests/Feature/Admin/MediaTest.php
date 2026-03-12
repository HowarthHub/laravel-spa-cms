<?php

use App\Models\MediaItemModel;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

// --- Index ---

it('lists media for authorised user', function () {
    actingAsSuperAdmin();
    MediaItemModel::factory()->count(3)->create();

    $this->get(route('admin.media.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Admin/Media/MediaIndexPage')
            ->has('media.data', 3)
        );
});

// --- Browse (JSON) ---

it('returns media items as JSON for browse endpoint', function () {
    actingAsSuperAdmin();
    MediaItemModel::factory()->count(2)->create();

    $this->getJson(route('admin.media.browse'))
        ->assertOk()
        ->assertJsonStructure(['data']);
});

// --- Store (Upload) ---

it('uploads a file and returns JSON', function () {
    Storage::fake('public');
    $user = actingAsSuperAdmin();

    $file = UploadedFile::fake()->image('test-image.jpg', 100, 100);

    $this->postJson(route('admin.media.store'), ['file' => $file])
        ->assertCreated()
        ->assertJsonStructure(['id', 'title']);

    $this->assertDatabaseHas('media_items', ['uploaded_by' => $user->id]);
});

// --- Update ---

it('updates media metadata', function () {
    actingAsSuperAdmin();
    $media = MediaItemModel::factory()->create();

    $this->putJson(route('admin.media.update', $media), [
        'title' => 'Updated Title',
        'alt_text' => 'Updated Alt',
    ])->assertOk();

    expect($media->fresh())
        ->title->toBe('Updated Title')
        ->alt_text->toBe('Updated Alt');
});

// --- Destroy ---

it('deletes a media item', function () {
    actingAsSuperAdmin();
    $media = MediaItemModel::factory()->create();

    $this->delete(route('admin.media.destroy', $media))
        ->assertRedirect(route('admin.media.index'));

    $this->assertDatabaseMissing('media_items', ['id' => $media->id]);
});

it('deletes a media item via JSON request', function () {
    actingAsSuperAdmin();
    $media = MediaItemModel::factory()->create();

    $this->deleteJson(route('admin.media.destroy', $media))
        ->assertOk()
        ->assertJson(['message' => 'Deleted.']);
});

// --- Auth ---

it('denies access without manage media permission', function () {
    actingAsViewer();

    $this->get(route('admin.media.index'))
        ->assertForbidden();
});

it('redirects guests to login', function () {
    $this->get(route('admin.media.index'))
        ->assertRedirect(route('login'));
});
