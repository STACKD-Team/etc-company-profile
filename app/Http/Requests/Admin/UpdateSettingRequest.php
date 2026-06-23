<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'vision' => ['nullable', 'string', 'max:2000'],
            'mission' => ['nullable', 'string', 'max:2000'],
            'general_info' => ['nullable', 'string', 'max:2000'],
            'address' => ['required', 'string', 'max:500'],
            'phone' => ['required', 'string', 'max:40'],
            'whatsapp' => ['nullable', 'string', 'max:40'],
            'email' => ['required', 'email', 'max:150'],
            'instagram' => ['nullable', 'string', 'max:500'],
            'hours' => ['required', 'string', 'max:200'],
            'bank_name' => ['nullable', 'string', 'max:100'],
            'bank_account_name' => ['nullable', 'string', 'max:150'],
            'bank_account_number' => ['nullable', 'string', 'max:80'],
            'payment_notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
