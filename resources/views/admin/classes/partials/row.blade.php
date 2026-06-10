@php($class = $item)

<tr>
    <td class="py-4 pr-4 font-heading font-bold text-etc-on-surface">{{ $class->name }}</td>
    <td class="py-4 pr-4 text-etc-on-muted">{{ $class->program?->name ?? '-' }}</td>
    <td class="py-4 pr-4 text-etc-on-muted">{{ $class->instructor?->full_name ?? $class->instructor?->name ?? '-' }}</td>
    <td class="py-4 pr-4 text-etc-on-muted">
        {{ $class->schedule_days ?? '-' }}
        <p class="mt-1 text-xs">{{ $class->schedule_time ?? '-' }} - {{ $class->room ?? 'Room belum diisi' }}</p>
    </td>
    <td class="py-4 pr-4 text-etc-on-muted">
        {{ $class->start_date?->format('d M Y') ?? '-' }}
        <p class="mt-1 text-xs">Selesai {{ $class->end_date?->format('d M Y') ?? '-' }}</p>
    </td>
    <td class="py-4 pr-4"><x-ui.badge :status="$class->status" /></td>
    <td class="py-4 pr-4">
        <x-ui.button :href="route('admin.classes.edit', $class)" size="sm" outlined>Edit</x-ui.button>
    </td>
</tr>
