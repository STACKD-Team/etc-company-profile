<?php

namespace App\Http\Requests\Admin;

use App\Models\Content;
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
            'type' => ['nullable', Rule::in(Content::TYPES)],
            'title' => ['required', 'string', 'max:200'],
            'slug' => ['nullable', 'string', 'max:220'],
            'body' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:4096'],
            'images' => ['nullable', 'array', 'max:8'],
            'images.*' => ['image', 'max:4096'],
            'meta' => ['nullable', 'array:caption,alt_text,category,event_date,location,website,since,role,rating'],
            'meta.caption' => ['nullable', 'string', 'max:255'],
            'meta.alt_text' => ['nullable', 'string', 'max:255'],
            'meta.category' => ['nullable', 'string', 'max:100'],
            'meta.event_date' => ['nullable', 'date'],
            'meta.location' => ['nullable', 'string', 'max:255'],
            'meta.website' => ['nullable', 'url', 'max:255'],
            'meta.since' => ['nullable', 'digits:4'],
            'meta.role' => ['nullable', 'string', 'max:120'],
            'meta.rating' => ['nullable', 'integer', 'min:1', 'max:5'],
            'display_order' => ['nullable', 'integer', 'min:-9999', 'max:9999'],
            'is_published' => ['nullable', 'boolean'],
        ];
    }
}
