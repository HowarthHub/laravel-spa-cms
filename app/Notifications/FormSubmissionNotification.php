<?php

namespace App\Notifications;

use App\Models\FormModel;
use App\Models\FormSubmissionModel;
use App\Models\SettingModel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FormSubmissionNotification extends Notification
{
    use Queueable;

    public function __construct(
        public FormModel $form,
        public FormSubmissionModel $submission,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        /** @var array<int, array{label: string, type: string, required?: bool}> $formFields */
        $formFields = $this->form->fields;

        /** @var array<string, mixed> $data */
        $data = $this->submission->data;

        $fields = [];
        foreach ($formFields as $field) {
            $key = str($field['label'])->slug('_')->toString();
            $value = $data[$key] ?? '';
            if (is_bool($value)) {
                $value = $value ? 'Yes' : 'No';
            }
            $fields[] = ['label' => $field['label'], 'value' => (string) $value];
        }

        $siteName = SettingModel::getCached('general', 'site_name') ?: config('app.name');

        return (new MailMessage)
            ->subject("New submission: {$this->form->name}")
            ->view('emails.form-submission', [
                'siteName' => $siteName,
                'formName' => $this->form->name,
                'submittedAt' => $this->submission->created_at->format('j M Y, g:ia'),
                'fields' => $fields,
            ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'form_id' => $this->form->id,
            'submission_id' => $this->submission->id,
        ];
    }
}
