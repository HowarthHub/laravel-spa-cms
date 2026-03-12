<?php

namespace App\Http\Requests\Admin\Media;

use Illuminate\Foundation\Http\FormRequest;

class MediaDestroyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manage media');
    }

    public function rules(): array
    {
        return [];
    }
}
