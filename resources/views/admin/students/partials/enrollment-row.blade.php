<tr>
    <td class="py-4 pr-4 text-etc-on-muted">{{ $item->courseClass?->program?->name ?? '-' }}</td>
    <td class="py-4 pr-4 font-heading font-bold text-etc-on-surface">{{ $item->courseClass?->name ?? '-' }}</td>
    <td class="py-4 pr-4 text-etc-on-muted">{{ $item->courseClass?->instructor?->full_name ?? $item->courseClass?->instructor?->name ?? '-' }}</td>
    <td class="py-4 pr-4 text-etc-on-muted">{{ $item->enrolled_at?->format('d M Y') ?? '-' }}</td>
    <td class="py-4 pr-4 text-etc-on-muted">{{ $item->completed_at?->format('d M Y') ?? '-' }}</td>
    <td class="py-4 pr-4"><x-ui.badge :status="$item->status" /></td>
    <td class="py-4 pr-4">
        @if ($item->reportCard)
            <x-ui.button :href="route('admin.report-cards.show', $item->reportCard)" size="sm" outlined>Rapor</x-ui.button>
        @else
            <span class="text-xs font-bold text-etc-on-muted">Belum ada</span>
        @endif
    </td>
</tr>
