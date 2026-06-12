<?php

namespace App\Http\Requests\Instructor;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'instructor';
    }

    public function rules(): array
    {
        return [
            'full_name' => ['nullable', 'string', 'max:150'],
            'mobile_phone' => ['nullable', 'string', 'max:20'],
            'instructor_position' => ['nullable', 'string', 'max:100'],
            'instructor_specialization' => ['nullable', 'string', 'max:100'],
            'instructor_bio' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
