<?php

namespace App\Http\Requests\Admin\Posts;

use Illuminate\Foundation\Http\FormRequest;

class PostBulkDraftRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('edit posts');
    }

    public function rules(): array
    {
        return [
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:posts,id'],
        ];
    }
}
