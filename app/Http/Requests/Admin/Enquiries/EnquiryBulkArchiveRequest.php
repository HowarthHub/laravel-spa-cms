<?php

namespace App\Http\Requests\Admin\Enquiries;

use Illuminate\Foundation\Http\FormRequest;

class EnquiryBulkArchiveRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manage enquiries');
    }

    public function rules(): array
    {
        return [
            'ids' => ['required', 'array'],
            'ids.*' => ['integer', 'exists:contact_enquiries,id'],
        ];
    }
}
