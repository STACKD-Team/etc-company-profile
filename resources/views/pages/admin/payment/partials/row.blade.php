@php
    $payment = $item;
    $statusLabels = [
        'pending_payment' => 'Menunggu Pembayaran',
        'paid' => 'Paid',
        'placement_test' => 'Placement Test',
        'enrolled' => 'Enrolled',
        'rejected' => 'Rejected',
        'cancelled' => 'Cancelled',
    ];
    $methods = ['qris' => 'QRIS', 'bank_transfer' => 'Transfer Bank', 'virtual_account' => 'Virtual Account', 'ewallet' => 'E-Wallet', 'manual' => 'Manual'];
    $paymentStatus = $payment->payment_status ?: match ($payment->status) {
        'paid', 'placement_test', 'enrolled' => 'paid',
        'cancelled' => 'expired',
        'rejected' => 'failed',
        default => 'waiting_payment',
    };
    $finalAmount = (float) ($payment->final_amount ?: $payment->payment_amount);
@endphp

<tr>
    <td class="py-4 pr-4">
        <p class="font-heading font-bold text-etc-on-surface">{{ $payment->applicant_name }}</p>
        <p class="mt-1 text-xs text-etc-on-muted">{{ $payment->registration_code }}</p>
    </td>
    <td class="py-4 pr-4 text-etc-on-muted">{{ $payment->program?->name ?? '-' }}</td>
    <td class="py-4 pr-4 text-etc-on-muted">
        <span class="block">{{ $methods[$payment->payment_method] ?? ($payment->payment_method ?? 'Midtrans') }}</span>
        <span class="mt-1 block text-xs text-etc-on-muted">{{ $payment->midtrans_order_id ?: '-' }}</span>
    </td>
    <td class="py-4 pr-4 font-heading font-bold text-etc-on-surface">{{ $finalAmount > 0 ? 'Rp '.number_format($finalAmount, 0, ',', '.') : '-' }}</td>
    <td class="py-4 pr-4">
        <x-ui.badge :status="$paymentStatus">{{ str($paymentStatus)->replace('_', ' ')->headline() }}</x-ui.badge>
    </td>
    <td class="py-4 pr-4">
        <x-ui.badge :status="$payment->status">{{ $statusLabels[$payment->status] ?? str($payment->status)->replace('_', ' ')->headline() }}</x-ui.badge>
    </td>
    <td class="py-4 pr-4 text-etc-on-muted">{{ $payment->paid_at?->format('d M Y H:i') ?? '-' }}</td>
    <td class="py-4 pr-4">
        <x-ui.button :href="route('admin.payment.show', ['payment' => $payment])" size="sm" outlined>Detail</x-ui.button>
    </td>
</tr>
