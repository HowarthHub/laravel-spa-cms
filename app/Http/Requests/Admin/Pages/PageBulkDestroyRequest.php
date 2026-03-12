<?php

namespace App\Http\Requests\Admin\Pages;

use Illuminate\Foundation\Http\FormRequest;

class PageBulkDestroyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('delete pages');
    }

    public function rules(): array
    {
        return [
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:pages,id'],
        ];
    }
}
