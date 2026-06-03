<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Public\StoreRegistrationRequest;
use App\Models\Program;
use App\Services\RegistrationService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\URL;

class RegistrationController extends Controller
{
    public function create(?string $program = null): View
    {
        $programs = Program::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        $selectedProgram = $this->resolveProgram($program) ?? $programs->first();

        return view('public.registration.create', [
            'programs' => $programs,
            'selectedProgram' => $selectedProgram,
            'applyingForOptions' => self::applyingForOptions(),
            'preferredDayOptions' => self::preferredDayOptions(),
            'preferredTimeOptions' => self::preferredTimeOptions(),
        ]);
    }

    public function store(StoreRegistrationRequest $request, RegistrationService $registrations): RedirectResponse
    {
        $registration = $registrations->createFromOnlineForm($request->validated());

        return redirect()
            ->to(URL::signedRoute('registrations.payment.show', ['registration' => $registration]))
            ->with('status', 'Pendaftaran berhasil disimpan. Silakan lanjutkan pembayaran.');
    }

    public static function applyingForOptions(): array
    {
        return [
            'tk' => 'TK',
            'pre_super_toddlers' => 'Pre/Super Toddlers',
            'sd_super_toddlers' => 'SD/Super Toddlers',
            'smp_teen' => 'SMP/Teen',
            'sma_excel_teen' => 'SMA/Excel Teen',
            'adult_university' => 'Dewasa/Adult University',
            'private' => 'Khusus/Private',
            'test_toefl_toeic_ielts' => 'Test TOEFL/TOEIC/IELTS',
            'prep_toefl_toeic_ielts_un' => 'Preparation TOEFL/TOEIC/IELTS/UN',
        ];
    }

    public static function preferredDayOptions(): array
    {
        return [
            'mon_wed' => 'Mon-Wed',
            'tue_thu' => 'Tues-Thurs',
            'wed_fri' => 'Wed-Fri',
            'sat_sun' => 'Sat-Sun',
            'request' => 'Request Schedule',
        ];
    }

    public static function preferredTimeOptions(): array
    {
        return [
            '09.00-10.30' => '09.00-10.30',
            '11.00-12.30' => '11.00-12.30',
            '13.00-14.30' => '13.00-14.30',
            '15.00-16.30' => '15.00-16.30',
            '17.00-18.30' => '17.00-18.30',
        ];
    }

    protected function resolveProgram(?string $program): ?Program
    {
        if (! $program) {
            return null;
        }

        return Program::query()
            ->where('is_active', true)
            ->where(fn ($query) => $query->whereKey($program)->orWhere('slug', $program))
            ->first();
    }
}
