@php
    $paymentStatus = $item->payment_status ?: match ($item->status) {
        'paid', 'placement_test', 'enrolled' => 'paid',
        'cancelled' => 'cancelled',
        'rejected' => 'failed',
        default => 'waiting_payment',
    };
    $statusLabel = $statusLabels[$paymentStatus] ?? str($paymentStatus)->replace('_', ' ')->headline();
    $statusColor = $statusColors[$paymentStatus] ?? 'primary';
    $originalAmount = (float) ($item->original_amount ?? $item->payment_amount ?? 0);
    $discountAmount = (float) ($item->discount_amount ?? 0);
    $finalAmount = (float) ($item->final_amount ?? max($originalAmount - $discountAmount, 0));
    $promotionTitle = $item->program_promotion_title;
@endphp

<tr class="group">
    <td class="py-4 pr-4">
        <p class="font-heading font-bold text-etc-on-surface">{{ $item->registration_code }}</p>
        <p class="mt-1 text-xs text-etc-on-muted">{{ $item->applicant_name }} - {{ $item->created_at?->format('d M Y') ?? '-' }}</p>
    </td>
    <td class="py-4 pr-4 text-etc-on-muted">{{ $item->program?->name ?? 'Program ETC Planet' }}</td>
    <td class="py-4 pr-4">
        <x-ui.badge status="info">{{ $methods[$item->payment_method] ?? ($item->payment_method ?? 'Midtrans') }}</x-ui.badge>
    </td>
    <td class="py-4 pr-4">
        <p class="text-xs font-bold uppercase text-etc-on-muted">Nominal akhir</p>
        <p class="mt-1 font-heading font-bold text-etc-on-surface">{{ $finalAmount > 0 ? 'Rp '.number_format($finalAmount, 0, ',', '.') : '-' }}</p>
        <p class="mt-1 text-xs text-etc-on-muted">
            Nominal asli: {{ $originalAmount > 0 ? 'Rp '.number_format($originalAmount, 0, ',', '.') : '-' }}
            - Potongan: {{ $discountAmount > 0 ? 'Rp '.number_format($discountAmount, 0, ',', '.') : 'Rp 0' }}
        </p>
        @if ($promotionTitle)
            <p class="mt-1 text-xs font-semibold text-etc-magenta">{{ $promotionTitle }}</p>
        @endif
    </td>
    <td class="py-4 pr-4"><x-ui.badge :status="$paymentStatus" :color="$statusColor">{{ $statusLabel }}</x-ui.badge></td>
    <td class="py-4 pr-4">
        <span class="block">{{ $item->paid_at?->format('d M Y') ?? '-' }}</span>
        @if ($item->payment_expires_at)
            <span class="mt-1 block text-xs text-etc-on-muted">Exp: {{ $item->payment_expires_at->format('d M Y H:i') }}</span>
        @endif
    </td>
    <td class="py-4 pr-4">
        <x-ui.icon-button
            :href="route('student.payments.show', ['payment' => $item])"
            icon="heroicon-m-eye"
            label="Detail pembayaran"
            size="sm"
            outlined
        />
    </td>
</tr>
