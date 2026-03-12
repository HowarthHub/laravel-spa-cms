<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Posts\PostBulkDestroyRequest;
use App\Http\Requests\Admin\Posts\PostDestroyRequest;
use App\Http\Requests\Admin\Posts\PostIndexRequest;
use App\Http\Requests\Admin\Posts\PostStoreRequest;
use App\Http\Requests\Admin\Posts\PostUpdateRequest;
use App\Models\PostModel;
use App\Services\Interfaces\CategoryServiceInterface;
use App\Services\Interfaces\PostServiceInterface;
use App\Services\Interfaces\TagServiceInterface;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class PostController extends Controller
{
    public function __construct(
        private readonly PostServiceInterface $postService,
        private readonly CategoryServiceInterface $categoryService,
        private readonly TagServiceInterface $tagService
    ) {}

    public function index(PostIndexRequest $request): Response
    {
        $posts = $this->postService->getPaginatedList($request->validated());

        return Inertia::render('Admin/Posts/PostIndexPage', [
            'posts' => $posts,
            'filters' => $request->only(['search', 'status', 'category_id']),
            'categories' => $this->categoryService->getAll(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Posts/PostCreatePage', [
            'categories' => $this->categoryService->getAll(),
            'tags' => $this->tagService->getAll(),
        ]);
    }

    public function store(PostStoreRequest $request): RedirectResponse
    {
        $this->postService->create($request->validated());

        return redirect()->route('admin.posts.index')->with('success', 'Post created.');
    }

    public function edit(PostModel $post): Response
    {
        $post->load(['categories', 'tags']);

        return Inertia::render('Admin/Posts/PostEditPage', [
            'post' => $post,
            'categories' => $this->categoryService->getAll(),
            'tags' => $this->tagService->getAll(),
        ]);
    }

    public function update(PostUpdateRequest $request, PostModel $post): RedirectResponse
    {
        $this->postService->update($post, $request->validated());

        return redirect()->route('admin.posts.index')->with('success', 'Post updated.');
    }

    public function destroy(PostDestroyRequest $request, PostModel $post): RedirectResponse
    {
        $this->postService->delete($post);

        return redirect()->route('admin.posts.index')->with('success', 'Post deleted.');
    }

    public function bulkDestroy(PostBulkDestroyRequest $request): RedirectResponse
    {
        $this->postService->bulkDelete($request->validated()['ids']);

        return redirect()->route('admin.posts.index')->with('success', 'Posts deleted.');
    }
}
