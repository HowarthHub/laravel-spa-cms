<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Media\MediaDestroyRequest;
use App\Http\Requests\Admin\Media\MediaIndexRequest;
use App\Http\Requests\Admin\Media\MediaStoreRequest;
use App\Http\Requests\Admin\Media\MediaUpdateRequest;
use App\Models\MediaItemModel;
use App\Services\Interfaces\MediaServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class MediaController extends Controller
{
    public function __construct(
        private readonly MediaServiceInterface $mediaService
    ) {}

    public function index(MediaIndexRequest $request): Response
    {
        return Inertia::render('Admin/Media/MediaIndexPage', [
            'media' => $this->mediaService->getPaginatedList($request->validated()),
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * JSON endpoint for the media picker modal.
     */
    public function browse(MediaIndexRequest $request): JsonResponse
    {
        return response()->json(
            $this->mediaService->getPaginatedList($request->validated())
        );
    }

    public function store(MediaStoreRequest $request): JsonResponse
    {
        $item = $this->mediaService->upload(
            $request->file('file'),
            $request->user()->id
        );

        return response()->json([
            'id' => $item->id,
            'title' => $item->title,
            'url' => $item->getFirstMediaUrl('files'),
            'thumb' => $item->getFirstMediaUrl('files'),
            'size' => $item->getFirstMedia('files')?->size,
            'mime' => $item->getFirstMedia('files')?->mime_type,
        ], 201);
    }

    public function update(MediaUpdateRequest $request, MediaItemModel $mediaItem): JsonResponse
    {
        $item = $this->mediaService->update($mediaItem, $request->validated());

        return response()->json(['message' => 'Updated.']);
    }

    public function destroy(MediaDestroyRequest $request, MediaItemModel $mediaItem): RedirectResponse|JsonResponse
    {
        $this->mediaService->delete($mediaItem);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Deleted.']);
        }

        return redirect()->route('admin.media.index')->with('success', 'Media deleted.');
    }
}
