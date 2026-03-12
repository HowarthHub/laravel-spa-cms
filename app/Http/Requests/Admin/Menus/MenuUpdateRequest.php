<?php

namespace App\Http\Requests\Admin\Menus;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MenuUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manage menus');
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'handle' => ['sometimes', 'string', 'max:255', Rule::unique('menus', 'handle')->ignore($this->route('menu'))],
            'tree' => ['sometimes', 'array'],
            'tree.*.label' => ['required_with:tree', 'string', 'max:255'],
            'tree.*.type' => ['required_with:tree', 'string', 'in:page,post,custom'],
            'tree.*.url' => ['nullable', 'string', 'max:2048'],
            'tree.*.target' => ['nullable', 'string', 'in:_self,_blank'],
            'tree.*.children' => ['nullable', 'array'],
        ];
    }
}
