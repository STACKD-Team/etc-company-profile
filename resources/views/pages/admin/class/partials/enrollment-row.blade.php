@php($enrollment = $item)

<tr>
    <td class="py-4 pr-4">
        @if ($enrollment->user)
            <a href="{{ route('admin.student.show', $enrollment->user) }}" class="font-heading font-bold text-etc-magenta hover:text-etc-primary">
                {{ $enrollment->user->full_name ?? $enrollment->user->name }}
            </a>
            <p class="mt-1 text-xs text-etc-on-muted">{{ $enrollment->user->email }}</p>
        @else
            -
        @endif
    </td>
    <td class="py-4 pr-4 text-etc-on-muted">{{ $enrollment->enrolled_at?->format('d M Y') ?? '-' }}</td>
    <td class="py-4 pr-4 text-etc-on-muted">{{ $enrollment->completed_at?->format('d M Y') ?? '-' }}</td>
    <td class="py-4 pr-4"><x-ui.badge :status="$enrollment->status" /></td>
    <td class="py-4 pr-4">
        @if ($enrollment->reportCard)
            <x-ui.button :href="route('admin.report-card.show', $enrollment->reportCard)" size="sm" outlined>Rapor</x-ui.button>
        @else
            <x-ui.badge status="draft">Belum ada</x-ui.badge>
        @endif
    </td>
    <td class="py-4 pr-4">
        <x-ui.button :href="route('admin.enrollment.show', $enrollment)" size="sm" outlined>Detail</x-ui.button>
    </td>
</tr>
