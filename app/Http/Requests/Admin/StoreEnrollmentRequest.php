<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEnrollmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'user_id' => ['required', Rule::exists('users', 'id')->where('role', 'student')],
            'class_id' => [
                'required',
                'exists:classes,id',
                Rule::unique('enrollments', 'class_id')->where(fn ($query) => $query->where('user_id', $this->input('user_id'))),
            ],
            'enrolled_at' => ['required', 'date'],
            'status' => ['nullable', Rule::in(['active', 'completed', 'dropped'])],
        ];
    }
}
