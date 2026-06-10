@php
    $registration = $item;
    $statusLabels = [
        'pending_payment' => 'Menunggu Pembayaran',
        'paid' => 'Paid',
        'placement_test' => 'Placement Test',
        'enrolled' => 'Enrolled',
        'rejected' => 'Rejected',
        'cancelled' => 'Cancelled',
    ];
@endphp

<tr>
    <td class="py-4 pr-4 font-heading font-bold text-etc-on-surface">{{ $registration->registration_code }}</td>
    <td class="py-4 pr-4">
        <p class="font-heading font-bold text-etc-on-surface">{{ $registration->applicant_name }}</p>
        <p class="mt-1 text-xs text-etc-on-muted">{{ $registration->applicant_email }}</p>
    </td>
    <td class="py-4 pr-4 text-etc-on-muted">{{ $registration->program?->name ?? '-' }}</td>
    <td class="py-4 pr-4 text-etc-on-muted">
        {{ str($registration->preferred_days ?? '-')->replace('_', ' ')->headline() }}
        <p class="mt-1 text-xs">{{ $registration->preferred_time ?? '-' }}</p>
    </td>
    <td class="py-4 pr-4">
        <x-ui.badge :status="$registration->status">{{ $statusLabels[$registration->status] ?? str($registration->status)->replace('_', ' ')->headline() }}</x-ui.badge>
    </td>
    <td class="py-4 pr-4 text-etc-on-muted">{{ $registration->created_at?->format('d M Y') ?? '-' }}</td>
    <td class="py-4 pr-4">
        <div class="flex flex-wrap gap-2">
            <x-ui.button :href="route('admin.registrations.show', $registration)" size="sm" outlined>Detail</x-ui.button>
            <x-ui.button :href="route('admin.registrations.edit', $registration)" size="sm" color="gray" outlined>Edit</x-ui.button>
        </div>
    </td>
</tr>
