<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProgramRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    public function rules(): array
    {
        $programId = $this->route('program')?->id;

        return [
            'name' => ['required', 'string', 'max:150'],
            'slug' => ['required', 'string', 'max:170', Rule::unique('programs', 'slug')->ignore($programId)],
            'category' => ['required', Rule::in(['english', 'mandarin', 'japanese', 'test_prep', 'soft_skills', 'other'])],
            'type' => ['required', Rule::in(['regular', 'private', 'one_on_one'])],
            'target_age' => ['nullable', Rule::in(['kids', 'teen', 'adult', 'university', 'all'])],
            'description' => ['nullable', 'string'],
            'duration_meetings' => ['nullable', 'integer', 'min:1'],
            'max_students' => ['nullable', 'integer', 'min:1'],
            'price' => ['required', 'numeric', 'min:0'],
            'registration_fee' => ['required', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
