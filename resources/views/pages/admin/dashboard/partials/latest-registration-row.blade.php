@php($registration = $item)

<tr>
    <td class="py-4 pr-4 font-heading font-bold text-etc-on-surface">{{ $registration->registration_code }}</td>
    <td class="py-4 pr-4 text-etc-on-surface">{{ $registration->applicant_name }}</td>
    <td class="py-4 pr-4 text-etc-on-muted">{{ $registration->program?->name ?? '-' }}</td>
    <td class="py-4 pr-4"><x-ui.badge :status="$registration->status" /></td>
    <td class="py-4 text-etc-on-muted">{{ $registration->created_at?->format('d M Y') ?? '-' }}</td>
</tr>
