<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePlacementTestResultRequest;
use App\Models\Registration;
use App\Services\RegistrationService;
use Illuminate\Http\RedirectResponse;

class PlacementTestResultController extends Controller
{
    public function store(StorePlacementTestResultRequest $request, Registration $registration, RegistrationService $registrations): RedirectResponse
    {
        $validated = $request->validated();

        $registrations->update($registration, [
            'placement_test_result' => $validated['placement_test_result'],
        ]);

        if (! empty($validated['class_id'])) {
            $registrations->assignClass($registration->refresh(), $validated['class_id']);
        }

        return redirect()->route('admin.placement-tests.show', $registration)->with('status', 'Hasil placement test tersimpan.');
    }
}
