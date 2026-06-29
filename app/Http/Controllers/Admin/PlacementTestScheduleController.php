<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePlacementTestScheduleRequest;
use App\Models\Registration;
use App\Services\RegistrationService;
use Illuminate\Http\RedirectResponse;

class PlacementTestScheduleController extends Controller
{
    public function store(StorePlacementTestScheduleRequest $request, Registration $registration, RegistrationService $registrations): RedirectResponse
    {
        $validated = $request->validated();

        $registrations->schedulePlacementTest($registration, $validated['placement_test_at']);

        return redirect()->route('admin.placement-test.show', $registration)->with('status', 'Jadwal placement test tersimpan.');
    }
}
