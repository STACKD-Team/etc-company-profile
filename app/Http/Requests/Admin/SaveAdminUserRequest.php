<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveAdminUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    public function rules(): array
    {
        $user = $this->route('student') ?? $this->route('instructor');
        $userId = is_object($user) ? $user->getKey() : null;
        $passwordRule = $this->isMethod('post') ? ['required', 'string', 'min:8'] : ['nullable', 'string', 'min:8'];

        return [
            'name' => ['required', 'string', 'max:150'],
            'full_name' => ['nullable', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($userId)],
            'password' => $passwordRule,
            'is_active' => ['nullable', 'boolean'],
            'no_induk' => ['nullable', 'string', 'max:20', Rule::unique('users', 'no_induk')->ignore($userId)],
            'mobile_phone' => ['nullable', 'string', 'max:20'],
            'status' => ['nullable', 'string', 'max:50'],
            'sex' => ['nullable', Rule::in(['M', 'F'])],
            'place_of_birth' => ['nullable', 'string', 'max:100'],
            'date_of_birth' => ['nullable', 'date'],
            'occupation_school' => ['nullable', 'string', 'max:150'],
            'address' => ['nullable', 'string'],
            'father_name' => ['nullable', 'string', 'max:150'],
            'mother_name' => ['nullable', 'string', 'max:150'],
            'instructor_position' => ['nullable', 'string', 'max:100'],
            'instructor_specialization' => ['nullable', 'string', 'max:100'],
            'instructor_bio' => ['nullable', 'string'],
            'show_on_team_page' => ['nullable', 'boolean'],
        ];
    }
}
