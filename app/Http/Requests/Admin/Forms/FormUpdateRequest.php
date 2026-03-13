<?php

namespace App\Http\Requests\Admin\Forms;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FormUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manage forms');
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'handle' => ['nullable', 'string', 'max:255', Rule::unique('forms', 'handle')->ignore($this->route('form'))],
            'fields' => ['required', 'array'],
            'fields.*' => ['required', 'array'],
            'fields.*.label' => ['required', 'string'],
            'fields.*.type' => ['required', 'in:text,email,tel,textarea,select,checkbox'],
            'fields.*.required' => ['nullable', 'boolean'],
            'fields.*.placeholder' => ['nullable', 'string'],
            'fields.*.options' => ['nullable', 'array'],
            'success_message' => ['nullable', 'string'],
            'notification_email' => ['nullable', 'email'],
            'is_active' => ['boolean'],
        ];
    }
}
