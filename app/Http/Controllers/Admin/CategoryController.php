<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Categories\CategoryDestroyRequest;
use App\Http\Requests\Admin\Categories\CategoryIndexRequest;
use App\Http\Requests\Admin\Categories\CategoryStoreRequest;
use App\Http\Requests\Admin\Categories\CategoryUpdateRequest;
use App\Models\CategoryModel;
use App\Services\Interfaces\CategoryServiceInterface;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CategoryController extends Controller
{
    public function __construct(
        private readonly CategoryServiceInterface $categoryService
    ) {}

    public function index(CategoryIndexRequest $request): Response
    {
        return Inertia::render('Admin/Categories/CategoryIndexPage', [
            'categories' => $this->categoryService->getPaginatedList($request->validated()),
            'filters' => $request->only(['search']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Categories/CategoryCreatePage', [
            'parentCategories' => CategoryModel::whereNull('parent_id')
                ->orderBy('name')
                ->get(['id', 'name']),
        ]);
    }

    public function store(CategoryStoreRequest $request): RedirectResponse
    {
        $this->categoryService->create($request->validated());

        return redirect()->route('admin.categories.index')->with('success', 'Category created.');
    }

    public function edit(CategoryModel $category): Response
    {
        return Inertia::render('Admin/Categories/CategoryEditPage', [
            'category' => $category->loadCount('posts'),
            'parentCategories' => CategoryModel::whereNull('parent_id')
                ->where('id', '!=', $category->id)
                ->orderBy('name')
                ->get(['id', 'name']),
        ]);
    }

    public function update(CategoryUpdateRequest $request, CategoryModel $category): RedirectResponse
    {
        $this->categoryService->update($category, $request->validated());

        return redirect()->route('admin.categories.index')->with('success', 'Category updated.');
    }

    public function destroy(CategoryDestroyRequest $request, CategoryModel $category): RedirectResponse
    {
        $this->categoryService->delete($category);

        return redirect()->route('admin.categories.index')->with('success', 'Category deleted.');
    }
}
