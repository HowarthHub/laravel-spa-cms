<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Services\ServiceBulkDestroyRequest;
use App\Http\Requests\Admin\Services\ServiceDestroyRequest;
use App\Http\Requests\Admin\Services\ServiceIndexRequest;
use App\Http\Requests\Admin\Services\ServiceStoreRequest;
use App\Http\Requests\Admin\Services\ServiceUpdateRequest;
use App\Models\ServiceModel;
use App\Services\Interfaces\ServiceServiceInterface;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ServiceController extends Controller
{
    public function __construct(
        private readonly ServiceServiceInterface $serviceService
    ) {}

    public function index(ServiceIndexRequest $request): Response
    {
        return Inertia::render('Admin/Services/ServiceIndexPage', [
            'services' => $this->serviceService->getPaginatedList($request->validated()),
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Services/ServiceCreatePage');
    }

    public function store(ServiceStoreRequest $request): RedirectResponse
    {
        $this->serviceService->create($request->validated());

        return redirect()->route('admin.services.index')->with('success', 'Service created.');
    }

    public function edit(ServiceModel $service): Response
    {
        return Inertia::render('Admin/Services/ServiceEditPage', [
            'service' => $service,
        ]);
    }

    public function update(ServiceUpdateRequest $request, ServiceModel $service): RedirectResponse
    {
        $this->serviceService->update($service, $request->validated());

        return redirect()->route('admin.services.index')->with('success', 'Service updated.');
    }

    public function destroy(ServiceDestroyRequest $request, ServiceModel $service): RedirectResponse
    {
        $this->serviceService->delete($service);

        return redirect()->route('admin.services.index')->with('success', 'Service deleted.');
    }

    public function bulkDestroy(ServiceBulkDestroyRequest $request): RedirectResponse
    {
        $this->serviceService->bulkDelete($request->validated()['ids']);

        return redirect()->route('admin.services.index')->with('success', 'Services deleted.');
    }
}
