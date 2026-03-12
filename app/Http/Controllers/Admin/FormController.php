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
}
