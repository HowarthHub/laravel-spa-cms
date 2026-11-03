<?php

namespace App\Http\Requests\Admin\Redirects;

use Illuminate\Foundation\Http\FormRequest;

class RedirectStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manage redirects');
    }

    public function rules(): array
    {
        return [
            'source_path' => ['required', 'string', 'max:255', 'starts_with:/', 'unique:redirects,source_path'],
            'destination_path' => ['required', 'string', 'max:255'],
            'status_code' => ['required', 'integer', 'in:301,302'],
            'is_active' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'source_path.starts_with' => 'The source path must start with a forward slash (/).',
            'source_path.unique' => 'A redirect for this source path already exists.',
        ];
    }
}
