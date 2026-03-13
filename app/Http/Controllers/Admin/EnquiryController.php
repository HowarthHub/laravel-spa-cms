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
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

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

    public function export(Request $request): StreamedResponse
    {
        $filename = 'enquiries-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($request) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, ['Name', 'Email', 'Phone', 'Subject', 'Message', 'Status', 'Created At']);

            ContactEnquiryModel::query()
                ->when($request->input('search'), function ($query, $search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('subject', 'like', "%{$search}%");
                    });
                })
                ->when($request->input('status'), function ($query, $status) {
                    $query->where('status', $status);
                })
                ->latest()
                ->chunk(500, function ($enquiries) use ($handle) {
                    foreach ($enquiries as $enquiry) {
                        fputcsv($handle, [
                            $enquiry->name,
                            $enquiry->email,
                            $enquiry->phone,
                            $enquiry->subject,
                            $enquiry->message,
                            $enquiry->status,
                            $enquiry->created_at->format('Y-m-d H:i:s'),
                        ]);
                    }
                });

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }
}
