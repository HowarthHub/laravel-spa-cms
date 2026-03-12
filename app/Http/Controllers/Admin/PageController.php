<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Pages\PageBulkDestroyRequest;
use App\Http\Requests\Admin\Pages\PageDestroyRequest;
use App\Http\Requests\Admin\Pages\PageIndexRequest;
use App\Http\Requests\Admin\Pages\PageStoreRequest;
use App\Http\Requests\Admin\Pages\PageUpdateRequest;
use App\Models\FormModel;
use App\Models\PageModel;
use App\Services\Interfaces\PageServiceInterface;
use Illuminate\Http\RedirectResponse;
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
            'pages' => PageModel::orderBy('title')->get(['id', 'title']),
            'forms' => FormModel::where('is_active', true)->orderBy('name')->get(['id', 'name']),
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
            'pages' => PageModel::where('id', '!=', $page->id)
                ->orderBy('title')
                ->get(['id', 'title']),
            'forms' => FormModel::where('is_active', true)->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(PageUpdateRequest $request, PageModel $page): RedirectResponse
    {
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
}
