<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Menus\MenuDestroyRequest;
use App\Http\Requests\Admin\Menus\MenuIndexRequest;
use App\Http\Requests\Admin\Menus\MenuStoreRequest;
use App\Http\Requests\Admin\Menus\MenuUpdateRequest;
use App\Models\MenuModel;
use App\Models\PageModel;
use App\Models\PostModel;
use App\Services\Interfaces\MenuServiceInterface;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class MenuController extends Controller
{
    public function __construct(
        private readonly MenuServiceInterface $menuService
    ) {}

    public function index(MenuIndexRequest $request): Response
    {
        $menus = $this->menuService->getAll();

        return Inertia::render('Admin/Menus/MenuIndexPage', [
            'menus' => $menus->map(fn (MenuModel $m) => [
                'id' => $m->id,
                'name' => $m->name,
                'handle' => $m->handle,
                'items_count' => $m->items()->count(),
                'updated_at' => $m->updated_at,
            ]),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Menus/MenuCreatePage');
    }

    public function store(MenuStoreRequest $request): RedirectResponse
    {
        $menu = $this->menuService->create($request->validated());

        return redirect()->route('admin.menus.edit', $menu)->with('success', 'Menu created.');
    }

    public function edit(MenuModel $menu): Response
    {
        return Inertia::render('Admin/Menus/MenuEditPage', [
            'menu' => $menu,
            'tree' => $this->menuService->getTree($menu->id),
            'pages' => PageModel::published()->orderBy('title')->get(['id', 'title', 'slug']),
            'posts' => PostModel::published()->orderBy('title')->get(['id', 'title', 'slug']),
        ]);
    }

    public function update(MenuUpdateRequest $request, MenuModel $menu): RedirectResponse
    {
        $data = $request->validated();

        if (isset($data['name'])) {
            $this->menuService->update($menu, [
                'name' => $data['name'],
                'handle' => $data['handle'] ?? $menu->handle,
            ]);
        }

        if (isset($data['tree'])) {
            $this->menuService->saveTree($menu->id, $data['tree']);
        }

        return redirect()->route('admin.menus.edit', $menu)->with('success', 'Menu saved.');
    }

    public function destroy(MenuDestroyRequest $request, MenuModel $menu): RedirectResponse
    {
        $this->menuService->delete($menu);

        return redirect()->route('admin.menus.index')->with('success', 'Menu deleted.');
    }
}
