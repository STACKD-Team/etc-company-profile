<tr>
    <td class="py-4 pr-4 font-heading font-bold text-etc-on-surface">
        <a href="{{ route('instructor.classes.show', $item) }}" class="text-etc-magenta hover:text-etc-primary">{{ $item->name }}</a>
    </td>
    <td class="py-4 pr-4 text-etc-on-muted">{{ $item->program?->name ?? '-' }}</td>
    <td class="py-4 pr-4">
        <span class="block">{{ $item->schedule_days ?? '-' }}</span>
        <span class="mt-1 block text-xs text-etc-on-muted">{{ $item->schedule_time ?? '-' }}</span>
        <span class="mt-1 block text-xs text-etc-on-muted">{{ $item->room ?? '-' }}</span>
    </td>
    <td class="py-4 pr-4">{{ $item->enrollments_count }}</td>
    <td class="py-4 pr-4"><x-ui.badge :status="$item->status" /></td>
    <td class="py-4 pr-4">
        <x-ui.button :href="route('instructor.classes.show', $item)" size="sm" outlined>
            Detail
        </x-ui.button>
    </td>
</tr>
