<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use App\Services\RegistrationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PlacementTestResultController extends Controller
{
    public function store(Request $request, Registration $registration, RegistrationService $registrations): RedirectResponse
    {
        $validated = $request->validate([
            'placement_test_result' => ['required', 'string', 'max:5000'],
            'class_id' => ['nullable', 'integer', 'exists:classes,id'],
        ]);

        $registrations->update($registration, [
            'placement_test_result' => $validated['placement_test_result'],
        ]);

        if (! empty($validated['class_id'])) {
            $registrations->assignClass($registration->refresh(), $validated['class_id']);
        }

        return redirect()->route('admin.placement-tests.show', $registration)->with('status', 'Hasil placement test tersimpan.');
    }
}
