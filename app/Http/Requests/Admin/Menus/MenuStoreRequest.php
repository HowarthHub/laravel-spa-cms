<?php

namespace App\Http\Requests\Admin\Menus;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class MenuStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manage menus');
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'handle' => ['required', 'string', 'max:255', 'unique:menus,handle'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if (! $this->handle) {
            $this->merge([
                'handle' => Str::slug($this->name),
            ]);
        }
    }
}
