<?php

namespace App\Http\Requests\Admin\Settings;

use Illuminate\Foundation\Http\FormRequest;

class SettingUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manage settings');
    }

    public function rules(): array
    {
        return [
            'tab' => ['nullable', 'string'],
            'settings' => ['required', 'array'],
            'settings.*' => ['nullable', 'string', 'max:10000'],
        ];
    }
}
