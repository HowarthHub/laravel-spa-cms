<?php

namespace App\Http\Requests\Admin\Pages;

use Illuminate\Foundation\Http\FormRequest;

class PageIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('view pages');
    }

    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'in:draft,published,scheduled'],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
