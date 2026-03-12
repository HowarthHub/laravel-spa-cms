<?php

namespace App\Http\Requests\Admin\Pages;

use Illuminate\Foundation\Http\FormRequest;

class PageDestroyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('delete pages');
    }

    public function rules(): array
    {
        return [];
    }
}
