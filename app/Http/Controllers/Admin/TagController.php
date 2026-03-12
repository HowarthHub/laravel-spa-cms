<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Tags\TagDestroyRequest;
use App\Http\Requests\Admin\Tags\TagIndexRequest;
use App\Http\Requests\Admin\Tags\TagStoreRequest;
use App\Http\Requests\Admin\Tags\TagUpdateRequest;
use App\Models\TagModel;
use App\Services\Interfaces\TagServiceInterface;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class TagController extends Controller
{
    public function __construct(
        private readonly TagServiceInterface $tagService
    ) {}

    public function index(TagIndexRequest $request): Response
    {
        return Inertia::render('Admin/Tags/TagIndexPage', [
            'tags' => $this->tagService->getPaginatedList($request->validated()),
            'filters' => $request->only(['search']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Tags/TagCreatePage');
    }

    public function store(TagStoreRequest $request): RedirectResponse
    {
        $this->tagService->create($request->validated());

        return redirect()->route('admin.tags.index')->with('success', 'Tag created.');
    }

    public function edit(TagModel $tag): Response
    {
        return Inertia::render('Admin/Tags/TagEditPage', [
            'tag' => $tag->loadCount('posts'),
        ]);
    }

    public function update(TagUpdateRequest $request, TagModel $tag): RedirectResponse
    {
        $this->tagService->update($tag, $request->validated());

        return redirect()->route('admin.tags.index')->with('success', 'Tag updated.');
    }

    public function destroy(TagDestroyRequest $request, TagModel $tag): RedirectResponse
    {
        $this->tagService->delete($tag);

        return redirect()->route('admin.tags.index')->with('success', 'Tag deleted.');
    }
}
