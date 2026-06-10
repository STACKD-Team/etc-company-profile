<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\Registration;
use App\Services\RegistrationService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function __construct(private RegistrationService $registrationService) {}

    public function index(Request $request): View
    {
        return view('admin.payments.index', [
            'payments' => $this->registrationService->paginatePaymentVerifications($request->only([
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

    public function show(Registration $payment): View
    {
        return view('admin.payments.show', [
            'payment' => $payment->load(['user', 'program', 'courseClass']),
        ]);
    }
}
