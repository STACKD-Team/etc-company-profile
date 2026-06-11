<?php

namespace App\Http\Requests\Instructor;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TableQueryRequest extends FormRequest
{
    private const PER_PAGE_OPTIONS = [10, 20, 50];

    public function authorize(): bool
    {
        return $this->user()?->role === 'instructor';
    }

    protected function prepareForValidation(): void
    {
        $perPage = filter_var($this->input('per_page'), FILTER_VALIDATE_INT);

        $this->merge([
            'per_page' => in_array($perPage, self::PER_PAGE_OPTIONS, true) ? $perPage : 10,
        ]);
    }

    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:200'],
            'name' => ['nullable', 'string', 'max:150'],
            'schedule' => ['nullable', 'string', 'max:150'],
            'program_id' => ['nullable', 'integer', 'min:1'],
            'class_id' => ['nullable', 'integer', 'min:1'],
            'student_id' => ['nullable', 'integer', 'min:1'],
            'students_count' => ['nullable', 'integer', 'min:0'],
            'total_score' => ['nullable', 'integer', 'min:0', 'max:100'],
            'enrolled_at' => ['nullable', 'date_format:Y-m-d'],
            'status' => [
                'nullable',
                Rule::in(['upcoming', 'ongoing', 'completed', 'cancelled', 'active', 'dropped']),
            ],
            'assessment_status' => [
                'nullable',
                Rule::in(['not_started', 'incomplete', 'complete', 'draft', 'published']),
            ],
            'sort' => ['nullable', 'string', 'max:50'],
            'direction' => ['nullable', Rule::in(['asc', 'desc'])],
            'per_page' => ['required', 'integer', Rule::in(self::PER_PAGE_OPTIONS)],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
