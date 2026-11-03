<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\FormModel;
use App\Models\FormSubmissionModel;
use App\Notifications\FormSubmissionNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class PublicFormController extends Controller
{
    public function submit(Request $request, FormModel $form): RedirectResponse
    {
        $rules = [];

        foreach ($form->fields as $field) {
            $fieldRules = [];

            if (! empty($field['required'])) {
                $fieldRules[] = 'required';
            } else {
                $fieldRules[] = 'nullable';
            }

            $fieldRules[] = match ($field['type'] ?? 'text') {
                'email' => 'email',
                'textarea' => 'string|max:5000',
                default => 'string|max:255',
            };

            $key = str($field['label'])->slug('_')->toString();
            $rules[$key] = implode('|', $fieldRules);
        }

        $validated = $request->validate($rules);

        $submission = FormSubmissionModel::create([
            'form_id' => $form->id,
            'data' => $validated,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        if ($form->notification_email) {
            Notification::route('mail', $form->notification_email)
                ->notify(new FormSubmissionNotification($form, $submission));
        }

        return back()->with('success', $form->success_message ?: 'Thank you for your submission.');
    }
}
