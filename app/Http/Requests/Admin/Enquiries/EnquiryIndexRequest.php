<?php

namespace App\Http\Requests\Admin\Enquiries;

use Illuminate\Foundation\Http\FormRequest;

class EnquiryIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('view enquiries');
    }

    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string', 'in:new,read,replied,archived'],
        ];
    }
}
