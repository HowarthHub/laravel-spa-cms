<?php

namespace App\Http\Requests\Admin\Services;

use Illuminate\Foundation\Http\FormRequest;

class ServiceDestroyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manage services');
    }

    public function rules(): array
    {
        return [];
    }
}
