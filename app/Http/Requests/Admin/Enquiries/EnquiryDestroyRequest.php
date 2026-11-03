<?php

namespace App\Http\Requests\Admin\Enquiries;

use Illuminate\Foundation\Http\FormRequest;

class EnquiryDestroyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manage enquiries');
    }

    public function rules(): array
    {
        return [];
    }
}
