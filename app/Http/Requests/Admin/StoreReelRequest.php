<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreReelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string'],
            'video' => ['required', 'file', 'mimetypes:video/mp4,video/quicktime,video/webm', 'max:51200'],
            'thumbnail' => ['nullable', 'image', 'max:4096'],
            'duration_seconds' => ['nullable', 'integer', 'min:1', 'max:3600'],
            'category' => ['nullable', Rule::in(['promosi', 'dokumentasi', 'edukasi', 'testimoni', 'event'])],
            'is_published' => ['nullable', 'boolean'],
        ];
    }
}
