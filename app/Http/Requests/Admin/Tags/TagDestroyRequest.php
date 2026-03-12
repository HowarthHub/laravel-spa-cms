<?php

namespace App\Http\Requests\Admin\Tags;

use Illuminate\Foundation\Http\FormRequest;

class TagDestroyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manage tags');
    }

    public function rules(): array
    {
        return [];
    }
}
