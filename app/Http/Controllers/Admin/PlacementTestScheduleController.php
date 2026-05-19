<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use App\Services\RegistrationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PlacementTestScheduleController extends Controller
{
    public function store(Request $request, Registration $registration, RegistrationService $registrations): RedirectResponse
    {
        $validated = $request->validate([
            'placement_test_at' => ['required', 'date'],
        ]);

        $registrations->schedulePlacementTest($registration, $validated['placement_test_at']);

        return redirect()->route('admin.placement-tests.show', $registration)->with('status', 'Jadwal placement test tersimpan.');
    }
}
