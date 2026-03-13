<?php

namespace App\Notifications;

use App\Models\FormModel;
use App\Models\FormSubmissionModel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FormSubmissionNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public FormModel $form,
        public FormSubmissionModel $submission,
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject("New submission: {$this->form->name}");

        /** @var array<int, array{label: string, type: string, required?: bool}> $fields */
        $fields = $this->form->fields;

        /** @var array<string, mixed> $data */
        $data = $this->submission->data;

        foreach ($fields as $field) {
            $key = str($field['label'])->slug('_')->toString();
            $value = $data[$key] ?? '—';

            $message->line("{$field['label']}: {$value}");
        }

        return $message;
    }

    /**
     * Get the array representation of the notification.
     *
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
