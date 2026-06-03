<?php

namespace App\Http\Requests\Public;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRegistrationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'program_id' => [
                'required',
                Rule::exists('programs', 'id')->where('is_active', true),
            ],
            'applying_for' => ['required', Rule::in([
                'tk',
                'pre_super_toddlers',
                'sd_super_toddlers',
                'smp_teen',
                'sma_excel_teen',
                'adult_university',
                'private',
                'test_toefl_toeic_ielts',
                'prep_toefl_toeic_ielts_un',
            ])],
            'full_name' => ['required', 'string', 'max:150'],
            'email' => [
                'required',
                'email',
                'max:150',
                Rule::unique('users', 'email')->where(fn ($query) => $query->whereIn('role', ['admin', 'instructor'])),
            ],
            'mobile_phone' => ['required', 'string', 'max:20'],
            'place_of_birth' => ['required', 'string', 'max:100'],
            'date_of_birth' => ['required', 'date', 'before:today'],
            'sex' => ['required', Rule::in(['M', 'F'])],
            'religion' => ['required', 'string', 'max:30'],
            'nationality' => ['required', 'string', 'max:50'],
            'occupation_school' => ['required', 'string', 'max:150'],
            'nisn' => ['nullable', 'string', 'max:20'],
            'nik' => ['nullable', 'string', 'max:20'],
            'kps_receiver' => ['required', 'boolean'],
            'no_kps' => ['nullable', 'required_if:kps_receiver,1', 'string', 'max:30'],
            'worthy_of_pip' => ['required', 'boolean'],
            'pip_reason' => ['nullable', 'required_if:worthy_of_pip,1', 'string', 'max:1000'],
            'no_kip' => ['nullable', 'string', 'max:30'],
            'address' => ['required', 'string', 'max:2000'],
            'rt_rw' => ['nullable', 'string', 'max:10'],
            'postal_code' => ['nullable', 'string', 'max:10'],
            'village' => ['nullable', 'string', 'max:100'],
            'sub_district' => ['nullable', 'string', 'max:100'],
            'district' => ['nullable', 'string', 'max:100'],
            'province' => ['nullable', 'string', 'max:100'],
            'living_with' => ['nullable', 'string', 'max:100'],
            'transportation' => ['nullable', 'string', 'max:50'],
            'mother_name' => ['required', 'string', 'max:150'],
            'father_name' => ['required', 'string', 'max:150'],
            'preferred_days' => ['required', Rule::in(['mon_wed', 'tue_thu', 'wed_fri', 'sat_sun', 'request'])],
            'preferred_time' => ['required', Rule::in(['09.00-10.30', '11.00-12.30', '13.00-14.30', '15.00-16.30', '17.00-18.30'])],
        ];
    }
}
