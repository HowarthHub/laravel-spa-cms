<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Enquiries\EnquiryBulkArchiveRequest;
use App\Http\Requests\Admin\Enquiries\EnquiryDestroyRequest;
use App\Http\Requests\Admin\Enquiries\EnquiryIndexRequest;
use App\Http\Requests\Admin\Enquiries\EnquiryUpdateRequest;
use App\Models\ContactEnquiryModel;
use App\Services\Interfaces\EnquiryServiceInterface;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class EnquiryController extends Controller
{
    public function __construct(
        private readonly EnquiryServiceInterface $enquiryService
    ) {}

    public function index(EnquiryIndexRequest $request): Response
    {
        return Inertia::render('Admin/Enquiries/EnquiryIndexPage', [
            'enquiries' => $this->enquiryService->getPaginatedList($request->validated()),
            'filters' => $request->only(['search', 'status']),
            'newCount' => $this->enquiryService->countNew(),
        ]);
    }

    public function show(ContactEnquiryModel $enquiry): Response
    {
        $this->enquiryService->markAsRead($enquiry);

        return Inertia::render('Admin/Enquiries/EnquiryShowPage', [
            'enquiry' => $enquiry->fresh(),
        ]);
    }

    public function update(EnquiryUpdateRequest $request, ContactEnquiryModel $enquiry): RedirectResponse
    {
        $data = $request->validated();

        if (isset($data['reply_note']) && $data['reply_note'] && ! $enquiry->replied_at) {
            $data['replied_at'] = now();
            $data['status'] = 'replied';
        }

        $this->enquiryService->update($enquiry, $data);

        return redirect()->route('admin.enquiries.show', $enquiry)->with('success', 'Enquiry updated.');
    }

    public function destroy(EnquiryDestroyRequest $request, ContactEnquiryModel $enquiry): RedirectResponse
    {
        $this->enquiryService->delete($enquiry);

        return redirect()->route('admin.enquiries.index')->with('success', 'Enquiry deleted.');
    }

    public function bulkArchive(EnquiryBulkArchiveRequest $request): RedirectResponse
    {
        $this->enquiryService->bulkArchive($request->validated()['ids']);

        return redirect()->route('admin.enquiries.index')->with('success', 'Enquiries archived.');
    }
}
