<?php

namespace App\Http\Requests\Admin\Forms;

use Illuminate\Foundation\Http\FormRequest;

class FormIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manage forms');
    }

    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
