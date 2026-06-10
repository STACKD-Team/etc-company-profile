<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function index(Request $request): View
    {
        $student = $request->user();

        return view('student.payments.index', [
            'student' => $student,
            'payments' => Registration::query()
                ->with(['program', 'courseClass'])
                ->where('user_id', $student->id)
                ->where($this->paymentRelevantFilter())
                ->latest('created_at')
                ->paginate(10),
            'statusLabels' => $this->statusLabels(),
            'statusColors' => $this->statusColors(),
            'methods' => $this->methods(),
        ]);
    }

    public function show(Request $request, Registration $payment): View
    {
        abort_unless($payment->user_id === $request->user()->id, 403);

        return view('student.payments.show', [
            'student' => $request->user(),
            'payment' => $payment->load(['program', 'courseClass']),
            'statusLabels' => $this->statusLabels(),
            'statusColors' => $this->statusColors(),
            'methods' => $this->methods(),
        ]);
    }

    protected function paymentRelevantFilter(): callable
    {
        return function (Builder $query): void {
            $query->whereNotNull('payment_amount')
                ->orWhereNotNull('payment_method')
                ->orWhereNotNull('payment_proof')
                ->orWhereNotNull('paid_at')
                ->orWhereIn('status', ['pending_payment', 'paid', 'placement_test', 'enrolled', 'rejected', 'cancelled']);
        };
    }

    protected function statusLabels(): array
    {
        return [
            'pending_payment' => 'Menunggu Pembayaran',
            'paid' => 'Lunas',
            'placement_test' => 'Menunggu Placement Test',
            'enrolled' => 'Aktif Belajar',
            'rejected' => 'Ditolak',
            'cancelled' => 'Dibatalkan',
            'waiting_payment' => 'Menunggu Pembayaran',
            'expired' => 'Kedaluwarsa',
            'failed' => 'Gagal',
        ];
    }

    protected function statusColors(): array
    {
        return [
            'pending_payment' => 'warning',
            'waiting_payment' => 'warning',
            'paid' => 'success',
            'placement_test' => 'warning',
            'enrolled' => 'success',
            'rejected' => 'danger',
            'cancelled' => 'danger',
            'expired' => 'danger',
            'failed' => 'danger',
        ];
    }

    protected function methods(): array
    {
        return [
            'qris' => 'QRIS',
            'bank_transfer' => 'Transfer Bank',
            'virtual_account' => 'Virtual Account',
            'ewallet' => 'E-Wallet',
            'manual' => 'Manual',
        ];
    }
}
