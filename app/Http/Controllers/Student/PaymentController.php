<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\TableQueryRequest;
use App\Models\Registration;
use App\Services\StudentPanelService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function index(TableQueryRequest $request, StudentPanelService $panel): View
    {
        $studentId = (int) $request->user()->id;

        return view('pages.student.payment.index', [
            'student' => $request->user(),
            'payments' => $panel->paginatePayments($studentId, $request->validated()),
            'programOptions' => $panel->programOptions($studentId),
            'classOptions' => $panel->classOptions($studentId),
            'statusLabels' => $panel->statusLabels(),
            'statusColors' => $panel->statusColors(),
            'paymentStatusOptions' => $panel->paymentStatusOptions(),
            'methods' => $panel->methods(),
        ]);
    }

    public function show(Request $request, Registration $payment, StudentPanelService $panel): View
    {
        $payment = $panel->ownedPayment((int) $request->user()->id, $payment);

        return view('pages.student.payment.show', [
            'student' => $request->user(),
            'payment' => $payment,
            'summary' => $panel->paymentSummary($payment),
            'statusLabels' => $panel->statusLabels(),
            'statusColors' => $panel->statusColors(),
            'methods' => $panel->methods(),
        ]);
    }
}
