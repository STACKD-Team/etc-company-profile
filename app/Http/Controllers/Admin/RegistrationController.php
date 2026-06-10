<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateRegistrationRequest;
use App\Models\Program;
use App\Models\Registration;
use App\Services\RegistrationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RegistrationController extends Controller
{
    public function __construct(private RegistrationService $registrationService) {}

    public function index(Request $request): View
    {
        return view('admin.registrations.index', [
            'registrations' => $this->registrationService->paginateAdminRegistrations($request->only([
                'search',
                'status',
                'program_id',
                'payment_method',
                'created_from',
                'created_to',
                'sort',
                'direction',
            ]), 12),
            'programs' => Program::query()->orderBy('name')->get(),
        ]);
    }

    public function show(Registration $registration): View
    {
        return view('admin.registrations.show', [
            'registration' => $registration->load(['user', 'program', 'courseClass']),
        ]);
    }

    public function edit(Registration $registration): View
    {
        return view('admin.registrations.edit', [
            'registration' => $registration->load(['program']),
            'programs' => Program::query()->orderBy('name')->get(),
        ]);
    }

    public function update(UpdateRegistrationRequest $request, Registration $registration): RedirectResponse
    {
        $this->registrationService->update($registration, $request->validated());

        return to_route('admin.registrations.show', $registration)->with('status', 'Data pendaftaran berhasil diperbarui.');
    }
}
