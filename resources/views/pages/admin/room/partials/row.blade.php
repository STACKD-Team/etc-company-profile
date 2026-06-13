@php($room = $item)

<tr>
    <td class="py-4 pr-4">
        <div class="min-w-[220px]">
            <p class="font-heading font-bold text-etc-on-surface">{{ $room->name }}</p>
            <p class="mt-1 line-clamp-1 text-xs text-etc-on-muted">{{ $room->description ?: '-' }}</p>
        </div>
    </td>
    <td class="py-4 pr-4 font-heading font-bold text-etc-on-surface">{{ $room->capacity ?: '-' }}</td>
    <td class="py-4 pr-4">
        <x-ui.badge :status="$room->is_active ? 'active' : 'draft'">{{ $room->is_active ? 'Aktif' : 'Nonaktif' }}</x-ui.badge>
    </td>
    <td class="py-4 pr-4 text-etc-on-muted">{{ (int) $room->display_order }}</td>
    <td class="py-4 pr-4">
        <div class="flex flex-wrap gap-2">
            <x-ui.button :href="route('admin.room.show', $room)" size="sm" outlined>Detail</x-ui.button>
            <x-ui.button :href="route('admin.room.edit', $room)" size="sm" color="gray" outlined>Edit</x-ui.button>
        </div>
    </td>
</tr>
