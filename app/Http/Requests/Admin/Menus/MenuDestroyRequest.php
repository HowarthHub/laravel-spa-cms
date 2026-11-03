<?php

namespace App\Http\Requests\Admin\Menus;

use Illuminate\Foundation\Http\FormRequest;

class MenuDestroyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manage menus');
    }

    public function rules(): array
    {
        return [];
    }
}
