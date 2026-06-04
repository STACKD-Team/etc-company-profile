<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SaveReportCardRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'enrollment_id' => ['required', 'integer', 'exists:enrollments,id'],
            'score_listening' => ['nullable', 'integer', 'min:0', 'max:20'],
            'score_vocabulary' => ['nullable', 'integer', 'min:0', 'max:20'],
            'score_structure' => ['nullable', 'integer', 'min:0', 'max:20'],
            'score_reading' => ['nullable', 'integer', 'min:0', 'max:20'],
            'score_writing' => ['nullable', 'integer', 'min:0', 'max:20'],
            'grade_pronunciation' => ['nullable', 'in:A,B,C,D'],
            'grade_sentence_arrangement' => ['nullable', 'in:A,B,C,D'],
            'grade_class_participation' => ['nullable', 'in:A,B,C,D'],
            'grade_questioning_skill' => ['nullable', 'in:A,B,C,D'],
            'grade_analyzing_skill' => ['nullable', 'in:A,B,C,D'],
            'total_score' => ['nullable', 'integer', 'min:0', 'max:100'],
            'final_grade' => ['nullable', 'in:A,B,C,D'],
            'next_class' => ['nullable', 'string', 'max:100'],
            'comments' => ['nullable', 'string'],
            'instructor_id' => ['nullable', 'integer', 'exists:users,id'],
            'academic_director_id' => ['nullable', 'integer', 'exists:users,id'],
            'managing_director_id' => ['nullable', 'integer', 'exists:users,id'],
            'issued_at' => ['nullable', 'date'],
        ];
    }
}
