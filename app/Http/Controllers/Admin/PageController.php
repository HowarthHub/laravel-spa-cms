<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Pages\PageBulkDestroyRequest;
use App\Http\Requests\Admin\Pages\PageBulkDraftRequest;
use App\Http\Requests\Admin\Pages\PageBulkPublishRequest;
use App\Http\Requests\Admin\Pages\PageDestroyRequest;
use App\Http\Requests\Admin\Pages\PageIndexRequest;
use App\Http\Requests\Admin\Pages\PageStoreRequest;
use App\Http\Requests\Admin\Pages\PageUpdateRequest;
use App\Models\FormModel;
use App\Models\PageModel;
use App\Models\SettingModel;
use App\Services\Interfaces\PageServiceInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class PageController extends Controller
{
    public function __construct(
        private readonly PageServiceInterface $pageService
    ) {}

    public function index(PageIndexRequest $request): Response
    {
        $pages = $this->pageService->getPaginatedList($request->validated());

        return Inertia::render('Admin/Pages/PageIndexPage', [
            'pages' => $pages,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Pages/PageCreatePage', [
            'templates' => config('cms.templates'),
            'blockTypes' => config('cms.block_types'),
            'pages' => PageModel::orderBy('title')->get(['id', 'title', 'slug']),
            'forms' => FormModel::where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'homepageId' => SettingModel::getCached('general', 'homepage'),
        ]);
    }

    public function store(PageStoreRequest $request): RedirectResponse
    {
        $this->pageService->create($request->validated());

        return redirect()->route('admin.pages.index')->with('success', 'Page created.');
    }

    public function edit(PageModel $page): Response
    {
        return Inertia::render('Admin/Pages/PageEditPage', [
            'page' => $page,
            'templates' => config('cms.templates'),
            'blockTypes' => config('cms.block_types'),
            'pages' => PageModel::where('id', '!=', $page->id)
                ->orderBy('title')
                ->get(['id', 'title', 'slug']),
            'forms' => FormModel::where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'homepageId' => SettingModel::getCached('general', 'homepage'),
        ]);
    }

    public function update(PageUpdateRequest $request, PageModel $page): RedirectResponse
    {
        $page->createRevision();

        $this->pageService->update($page, $request->validated());

        return redirect()->route('admin.pages.index')->with('success', 'Page updated.');
    }

    public function destroy(PageDestroyRequest $request, PageModel $page): RedirectResponse
    {
        $this->pageService->delete($page);

        return redirect()->route('admin.pages.index')->with('success', 'Page deleted.');
    }

    public function bulkDestroy(PageBulkDestroyRequest $request): RedirectResponse
    {
        $this->pageService->bulkDelete($request->validated()['ids']);

        return redirect()->route('admin.pages.index')->with('success', 'Pages deleted.');
    }

    public function bulkDraft(PageBulkDraftRequest $request): RedirectResponse
    {
        $this->pageService->bulkDraft($request->validated()['ids']);

        return redirect()->route('admin.pages.index')->with('success', 'Pages set to draft.');
    }

    public function bulkPublish(PageBulkPublishRequest $request): RedirectResponse
    {
        $this->pageService->bulkPublish($request->validated()['ids']);

        return redirect()->route('admin.pages.index')->with('success', 'Pages published.');
    }

    public function duplicate(PageModel $page): RedirectResponse
    {
        abort_unless(auth()->user()->can('create pages'), 403);

        $slug = Str::slug($page->slug.'-copy');

        $duplicate = PageModel::create([
            ...$page->only([
                'content', 'excerpt', 'template', 'sort_order',
                'meta_title', 'meta_description', 'og_image',
            ]),
            'title' => "Copy of {$page->title}",
            'slug' => $slug,
            'status' => 'draft',
            'published_at' => null,
            'parent_id' => null,
            'form_id' => null,
            'author_id' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        return redirect()->route('admin.pages.edit', $duplicate)->with('success', 'Page duplicated.');
    }
}
