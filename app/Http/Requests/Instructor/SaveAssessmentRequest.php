<?php

namespace App\Http\Requests\Instructor;

use Illuminate\Foundation\Http\FormRequest;

class SaveAssessmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'instructor';
    }

    public function rules(): array
    {
        return [
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
            'final_grade' => ['nullable', 'in:A,B,C,D'],
            'next_class' => ['nullable', 'string', 'max:100'],
            'comments' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
