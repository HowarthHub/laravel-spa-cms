<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Forms\FormDestroyRequest;
use App\Http\Requests\Admin\Forms\FormIndexRequest;
use App\Http\Requests\Admin\Forms\FormStoreRequest;
use App\Http\Requests\Admin\Forms\FormUpdateRequest;
use App\Models\FormModel;
use App\Models\FormSubmissionModel;
use App\Services\Interfaces\FormServiceInterface;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FormController extends Controller
{
    public function __construct(
        private readonly FormServiceInterface $formService
    ) {}

    public function index(FormIndexRequest $request): Response
    {
        $forms = $this->formService->getPaginatedList($request->validated());

        return Inertia::render('Admin/Forms/FormIndexPage', [
            'forms' => $forms,
            'filters' => $request->only(['search', 'is_active']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Forms/FormCreatePage');
    }

    public function store(FormStoreRequest $request): RedirectResponse
    {
        $this->formService->create($request->validated());

        return redirect()->route('admin.forms.index')->with('success', 'Form created.');
    }

    public function edit(FormModel $form): Response
    {
        $form->loadCount('submissions');

        return Inertia::render('Admin/Forms/FormEditPage', [
            'form' => $form,
        ]);
    }

    public function update(FormUpdateRequest $request, FormModel $form): RedirectResponse
    {
        $this->formService->update($form, $request->validated());

        return redirect()->route('admin.forms.index')->with('success', 'Form updated.');
    }

    public function destroy(FormDestroyRequest $request, FormModel $form): RedirectResponse
    {
        $this->formService->delete($form);

        return redirect()->route('admin.forms.index')->with('success', 'Form deleted.');
    }

    public function submissions(FormModel $form): Response
    {
        return Inertia::render('Admin/Forms/FormSubmissionsPage', [
            'form' => $form,
            'submissions' => $form->submissions()->latest()->paginate(25),
        ]);
    }

    public function destroySubmission(FormSubmissionModel $formSubmission): RedirectResponse
    {
        $formId = $formSubmission->form_id;
        $formSubmission->delete();

        return redirect()->route('admin.forms.submissions', $formId)->with('success', 'Submission deleted.');
    }

    public function exportSubmissions(FormModel $form): StreamedResponse
    {
        $fields = $form->fields ?? [];
        $headers = array_map(fn (array $field) => $field['label'], $fields);
        $headers[] = 'Created At';

        $filename = 'form-submissions-'.str($form->name)->slug().'-'.now()->format('Y-m-d').'.csv';

        return response()->streamDownload(function () use ($form, $fields, $headers) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, $headers);

            $form->submissions()
                ->latest()
                ->chunk(500, function ($submissions) use ($handle, $fields) {
                    foreach ($submissions as $submission) {
                        $row = [];
                        foreach ($fields as $field) {
                            $value = $submission->data[$field['label']] ?? '';
                            if (is_bool($value)) {
                                $value = $value ? 'Yes' : 'No';
                            }
                            $row[] = $value;
                        }
                        $row[] = $submission->created_at->format('Y-m-d H:i:s');

                        fputcsv($handle, $row);
                    }
                });

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }
}
