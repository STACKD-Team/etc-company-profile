@php($registration = $item)

<tr>
    <td class="py-4 pr-4 font-heading font-bold text-etc-on-surface">{{ $registration->registration_code }}</td>
    <td class="py-4 pr-4">
        <p class="font-heading font-bold text-etc-on-surface">{{ $registration->applicant_name }}</p>
        <p class="mt-1 text-xs text-etc-on-muted">{{ $registration->applicant_email }}</p>
    </td>
    <td class="py-4 pr-4"><x-ui.badge :status="$registration->payment_status" /></td>
    <td class="py-4 pr-4"><x-ui.badge :status="$registration->status" /></td>
    <td class="py-4 pr-4">
        <x-ui.button :href="route('admin.registration.show', $registration)" size="sm" outlined>Detail</x-ui.button>
    </td>
</tr>
