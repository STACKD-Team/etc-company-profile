<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRegistrationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'program_id' => ['required', 'exists:programs,id'],
            'applicant_name' => ['required', 'string', 'max:150'],
            'applicant_email' => ['required', 'email', 'max:150'],
            'applicant_phone' => ['required', 'string', 'max:20'],
            'preferred_days' => ['nullable', Rule::in(['mon_wed', 'tue_thu', 'wed_fri', 'sat_sun', 'request'])],
            'preferred_time' => ['nullable', 'string', 'max:20'],
            'payment_method' => ['nullable', Rule::in(['qris', 'bank_transfer', 'virtual_account', 'ewallet', 'manual'])],
            'payment_amount' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', Rule::in(['pending_payment', 'paid', 'placement_test', 'enrolled', 'rejected', 'cancelled'])],
            'notes' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
