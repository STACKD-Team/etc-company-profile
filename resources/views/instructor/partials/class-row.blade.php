<tr>
    <td class="py-4 pr-4 font-heading font-bold text-etc-on-surface">
        <a href="{{ route('instructor.classes.show', $item) }}" class="text-etc-magenta hover:text-etc-primary">{{ $item->name }}</a>
    </td>
    <td class="py-4 pr-4 text-etc-on-muted">{{ $item->program?->name ?? '-' }}</td>
    <td class="py-4 pr-4">{{ trim(($item->schedule_days ?? '-').' '.$item->schedule_time) }}</td>
    <td class="py-4 pr-4">{{ $item->status ?? '-' }}</td>
</tr>
