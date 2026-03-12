<?php

namespace App\Http\Requests\Admin\Enquiries;

use Illuminate\Foundation\Http\FormRequest;

class EnquiryUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manage enquiries');
    }

    public function rules(): array
    {
        return [
            'status' => ['sometimes', 'string', 'in:new,read,replied,archived'],
            'reply_note' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
