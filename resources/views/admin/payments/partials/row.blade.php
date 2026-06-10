@php
    $payment = $item;
    $statusLabels = [
        'pending_payment' => 'Menunggu Verifikasi',
        'paid' => 'Paid',
        'placement_test' => 'Placement Test',
        'enrolled' => 'Enrolled',
        'rejected' => 'Rejected',
        'cancelled' => 'Cancelled',
    ];
    $methods = ['qris' => 'QRIS', 'bank_transfer' => 'Transfer Bank', 'virtual_account' => 'Virtual Account', 'ewallet' => 'E-Wallet', 'manual' => 'Manual'];
@endphp

<tr>
    <td class="py-4 pr-4">
        <p class="font-heading font-bold text-etc-on-surface">{{ $payment->applicant_name }}</p>
        <p class="mt-1 text-xs text-etc-on-muted">{{ $payment->registration_code }}</p>
    </td>
    <td class="py-4 pr-4 text-etc-on-muted">{{ $payment->program?->name ?? '-' }}</td>
    <td class="py-4 pr-4 text-etc-on-muted">{{ $methods[$payment->payment_method] ?? ($payment->payment_method ?? '-') }}</td>
    <td class="py-4 pr-4 font-heading font-bold text-etc-on-surface">{{ $payment->payment_amount ? 'Rp '.number_format((float) $payment->payment_amount, 0, ',', '.') : '-' }}</td>
    <td class="py-4 pr-4">
        <x-ui.badge :status="$payment->status">{{ $statusLabels[$payment->status] ?? str($payment->status)->replace('_', ' ')->headline() }}</x-ui.badge>
    </td>
    <td class="py-4 pr-4 text-etc-on-muted">{{ $payment->paid_at?->format('d M Y H:i') ?? '-' }}</td>
    <td class="py-4 pr-4">
        <x-ui.button :href="route('admin.payments.show', ['payment' => $payment])" size="sm" outlined>Detail</x-ui.button>
    </td>
</tr>
