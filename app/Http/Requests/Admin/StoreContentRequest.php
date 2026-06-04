<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreContentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'type' => ['required', Rule::in(['page', 'gallery', 'partner', 'room', 'team_member_extra', 'setting'])],
            'title' => ['required', 'string', 'max:200'],
            'slug' => ['nullable', 'string', 'max:220'],
            'body' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:4096'],
            'images' => ['nullable', 'array', 'max:8'],
            'images.*' => ['image', 'max:4096'],
            'meta' => ['nullable', 'array'],
            'meta.*' => ['nullable', 'string', 'max:1000'],
            'display_order' => ['nullable', 'integer', 'min:-9999', 'max:9999'],
            'is_published' => ['nullable', 'boolean'],
        ];
    }
}
