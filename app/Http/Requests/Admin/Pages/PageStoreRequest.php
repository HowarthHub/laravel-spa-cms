<?php

namespace App\Http\Requests\Admin\Pages;

use Illuminate\Foundation\Http\FormRequest;

class PageStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create pages');
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'pre_heading' => ['nullable', 'string', 'max:255'],
            'content' => ['nullable', 'array'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'template' => ['nullable', 'string', 'max:100'],
            'status' => ['required', 'in:draft,published,scheduled'],
            'published_at' => ['nullable', 'date', 'required_if:status,scheduled'],
            'parent_id' => ['nullable', 'integer', 'exists:pages,id'],
            'sort_order' => ['nullable', 'integer'],
            'featured_image' => ['nullable', 'string'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'og_image' => ['nullable', 'string'],
            'form_id' => ['nullable', 'integer', 'exists:forms,id'],
        ];
    }
}
