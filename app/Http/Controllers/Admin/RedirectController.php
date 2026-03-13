<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Redirects\RedirectDestroyRequest;
use App\Http\Requests\Admin\Redirects\RedirectIndexRequest;
use App\Http\Requests\Admin\Redirects\RedirectStoreRequest;
use App\Http\Requests\Admin\Redirects\RedirectUpdateRequest;
use App\Models\RedirectModel;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class RedirectController extends Controller
{
    public function index(RedirectIndexRequest $request): Response
    {
        $redirects = RedirectModel::query()
            ->when($request->validated('search'), function ($query, $search) {
                $query->where('source_path', 'like', "%{$search}%")
                    ->orWhere('destination_path', 'like', "%{$search}%");
            })
            ->orderByDesc('updated_at')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Admin/Redirects/RedirectIndexPage', [
            'redirects' => $redirects,
            'filters' => $request->only(['search']),
        ]);
    }

    public function store(RedirectStoreRequest $request): RedirectResponse
    {
        RedirectModel::create($request->validated());

        return redirect()->route('admin.redirects.index')->with('success', 'Redirect created.');
    }

    public function update(RedirectUpdateRequest $request, RedirectModel $redirect): RedirectResponse
    {
        $redirect->update($request->validated());

        return redirect()->route('admin.redirects.index')->with('success', 'Redirect updated.');
    }

    public function destroy(RedirectDestroyRequest $request, RedirectModel $redirect): RedirectResponse
    {
        $redirect->delete();

        return redirect()->route('admin.redirects.index')->with('success', 'Redirect deleted.');
    }
}
