<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StorePlacementTestResultRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'placement_test_result' => ['required', 'string', 'max:5000'],
            'class_id' => ['nullable', 'integer', 'exists:classes,id'],
        ];
    }
}
