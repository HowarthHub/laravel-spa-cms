<?php

namespace App\Http\Requests\Admin\Posts;

use Illuminate\Foundation\Http\FormRequest;

class PostBulkPublishRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('publish posts');
    }

    public function rules(): array
    {
        return [
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:posts,id'],
        ];
    }
}
