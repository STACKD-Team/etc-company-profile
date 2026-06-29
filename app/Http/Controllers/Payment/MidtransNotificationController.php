<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Services\MidtransPaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MidtransNotificationController extends Controller
{
    public function __invoke(Request $request, MidtransPaymentService $payments): JsonResponse
    {
        $payload = $request->validate([
            'order_id' => ['required', 'string', 'max:180'],
            'transaction_status' => ['required', 'string', 'max:60'],
            'signature_key' => ['nullable', 'string'],
            'transaction_id' => ['nullable', 'string', 'max:180'],
            'payment_type' => ['nullable', 'string', 'max:80'],
            'fraud_status' => ['nullable', 'string', 'max:80'],
            'status_code' => ['nullable', 'string', 'max:20'],
            'gross_amount' => ['nullable', 'numeric'],
            'status_message' => ['nullable', 'string'],
        ]);

        $notification = $payments->handleNotification($payload, $request->all());

        return response()->json([
            'status' => $notification->processing_status,
            'notification_id' => $notification->id,
        ]);
    }
}
