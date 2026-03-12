<?php

namespace App\Http\Requests\Admin\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class DashboardIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('view dashboard');
    }

    public function rules(): array
    {
        return [];
    }
}
