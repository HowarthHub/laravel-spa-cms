<?php

namespace App\Http\Requests\Admin\Posts;

use Illuminate\Foundation\Http\FormRequest;

class PostDestroyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('delete posts');
    }

    public function rules(): array
    {
        return [];
    }
}
