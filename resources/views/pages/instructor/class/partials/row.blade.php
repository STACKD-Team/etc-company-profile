<tr class="group">
    <td class="py-4 pr-4 font-heading font-bold text-etc-on-surface">
        <a
            href="{{ route('instructor.classes.show', $item) }}"
            class="rounded-selector text-etc-on-surface outline-none hover:text-etc-magenta focus-visible:ring-2 focus-visible:ring-etc-magenta focus-visible:ring-offset-2"
        >
            {{ $item->name }}
        </a>
    </td>
    <td class="py-4 pr-4 text-etc-on-muted">{{ $item->program?->name ?? '-' }}</td>
    <td class="py-4 pr-4">
        <span class="block">{{ $item->schedule_days ?? '-' }}</span>
        <span class="mt-1 block text-xs text-etc-on-muted">{{ $item->schedule_time ?? '-' }}</span>
        <span class="mt-1 block text-xs text-etc-on-muted">{{ $item->room_label ?? '-' }}</span>
    </td>
    <td class="py-4 pr-4">{{ $item->enrollments_count }}</td>
    <td class="py-4 pr-4"><x-ui.badge :status="$item->status" /></td>
    <td class="py-4 pr-4">
        <x-ui.icon-button
            :href="route('instructor.classes.show', $item)"
            icon="heroicon-m-arrow-right"
            :label="'Buka detail '.$item->name"
            size="sm"
            outlined
        />
    </td>
</tr>
