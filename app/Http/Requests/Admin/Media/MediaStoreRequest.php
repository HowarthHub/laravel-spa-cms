<?php

namespace App\Http\Requests\Admin\Media;

use Illuminate\Foundation\Http\FormRequest;

class MediaStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manage media');
    }

    public function rules(): array
    {
        $maxKb = config('cms.media.max_size_kb');
        $mimes = implode(',', array_map(
            fn ($m) => str_replace('image/', '', str_replace('application/', '', $m)),
            config('cms.media.allowed_mimes')
        ));

        return [
            'file' => ['required', 'file', "max:{$maxKb}", "mimetypes:" . implode(',', config('cms.media.allowed_mimes'))],
        ];
    }
}
