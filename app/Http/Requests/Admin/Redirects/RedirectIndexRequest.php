<?php

namespace App\Http\Requests\Admin\Redirects;

use Illuminate\Foundation\Http\FormRequest;

class RedirectIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manage redirects');
    }

    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:255'],
        ];
    }
}
