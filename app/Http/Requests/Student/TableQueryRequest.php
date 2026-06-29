<?php

namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TableQueryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'student';
    }

    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:200'],
            'status' => ['nullable', Rule::in(['active', 'completed', 'dropped', 'pending_payment', 'paid', 'placement_test', 'enrolled', 'rejected', 'cancelled'])],
            'program_id' => ['nullable', 'integer', 'min:1'],
            'class_id' => ['nullable', 'integer', 'min:1'],
            'instructor_id' => ['nullable', 'integer', 'min:1'],
            'payment_status' => ['nullable', Rule::in(['waiting_payment', 'pending_payment', 'paid', 'expired', 'failed', 'cancelled', 'placement_test', 'enrolled', 'rejected'])],
            'payment_method' => ['nullable', Rule::in(['qris', 'bank_transfer', 'virtual_account', 'ewallet'])],
            'report_status' => ['nullable', Rule::in(['published', 'with_file', 'without_file'])],
            'final_grade' => ['nullable', Rule::in(['A', 'B', 'C', 'D'])],
            'issued_at' => ['nullable', 'date_format:Y-m-d'],
            'paid_at' => ['nullable', 'date_format:Y-m-d'],
            'sort' => ['nullable', 'string', 'max:50'],
            'direction' => ['nullable', Rule::in(['asc', 'desc'])],
            'per_page' => ['nullable', 'integer', Rule::in([10, 20, 50])],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
