@php($class = $item)

<tr>
    <td class="py-4 pr-4 font-heading font-bold text-etc-on-surface">{{ $class->name }}</td>
    <td class="py-4 pr-4 text-etc-on-muted">{{ $class->program?->name ?? '-' }}</td>
    <td class="py-4 pr-4 text-etc-on-muted">{{ $class->instructor?->full_name ?? $class->instructor?->name ?? '-' }}</td>
    <td class="py-4 pr-4"><x-ui.badge :status="$class->status">{{ str($class->status)->headline() }}</x-ui.badge></td>
    <td class="py-4 pr-4">
        <x-ui.button :href="route('admin.class.show', $class)" size="sm" outlined>Detail</x-ui.button>
    </td>
</tr>
