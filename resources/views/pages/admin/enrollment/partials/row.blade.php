@php($enrollment = $item)

<tr>
    <td class="py-4 pr-4">
        <p class="font-heading font-bold text-etc-on-surface">{{ $enrollment->user?->full_name ?? $enrollment->user?->name ?? '-' }}</p>
        <p class="mt-1 text-xs text-etc-on-muted">{{ $enrollment->user?->email ?? '-' }}</p>
    </td>
    <td class="py-4 pr-4">
        <p class="font-heading font-bold text-etc-on-surface">{{ $enrollment->courseClass?->name ?? '-' }}</p>
        <p class="mt-1 text-xs text-etc-on-muted">{{ $enrollment->courseClass?->program?->name ?? '-' }}</p>
    </td>
    <td class="py-4 pr-4 text-etc-on-muted">{{ $enrollment->enrolled_at?->format('d M Y') ?? '-' }}</td>
    <td class="py-4 pr-4 text-etc-on-muted">{{ $enrollment->completed_at?->format('d M Y') ?? '-' }}</td>
    <td class="py-4 pr-4"><x-ui.badge :status="$enrollment->status" /></td>
    <td class="py-4 pr-4">
        <x-ui.button :href="route('admin.enrollment.show', $enrollment)" size="sm" outlined>Detail</x-ui.button>
    </td>
</tr>
