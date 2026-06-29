<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DestroyAdminResourceRequest;
use App\Models\Registration;
use App\Services\RegistrationService;
use Illuminate\Http\RedirectResponse;

class PlacementTestClearController extends Controller
{
    public function __invoke(DestroyAdminResourceRequest $request, Registration $registration, RegistrationService $registrations): RedirectResponse
    {
        $request->validated();

        $registrations->update($registration, [
            'placement_test_at' => null,
            'placement_test_result' => null,
            'class_id' => null,
            'status' => 'paid',
        ]);

        return to_route('admin.placement-test.show', $registration)->with('status', 'Data placement test berhasil dikosongkan.');
    }
}
