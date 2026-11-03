<?php

namespace App\Http\Requests\Admin\Forms;

use Illuminate\Foundation\Http\FormRequest;

class FormDestroyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manage forms');
    }

    public function rules(): array
    {
        return [];
    }
}
