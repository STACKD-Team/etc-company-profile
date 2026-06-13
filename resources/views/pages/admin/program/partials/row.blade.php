@php($program = $item)

<tr>
    <td class="py-4 pr-4">
        <p class="font-heading font-bold text-etc-on-surface">{{ $program->name }}</p>
        <p class="mt-1 text-xs text-etc-on-muted">{{ $program->slug }}</p>
    </td>
    <td class="py-4 pr-4"><x-ui.badge status="primary">{{ str($program->category)->replace('_', ' ')->headline() }}</x-ui.badge></td>
    <td class="py-4 pr-4 text-etc-on-muted">{{ str($program->type)->replace('_', ' ')->headline() }}</td>
    <td class="py-4 pr-4 text-etc-on-muted">{{ str($program->target_age)->headline() }}</td>
    <td class="py-4 pr-4">
        <p class="font-heading font-bold text-etc-on-surface">Rp {{ number_format((float) $program->price, 0, ',', '.') }}</p>
        <p class="mt-1 text-xs text-etc-on-muted">Daftar Rp {{ number_format((float) $program->registration_fee, 0, ',', '.') }}</p>
    </td>
    <td class="py-4 pr-4"><x-ui.badge :status="$program->is_active ? 'active' : 'inactive'">{{ $program->is_active ? 'Aktif' : 'Nonaktif' }}</x-ui.badge></td>
    <td class="py-4 pr-4">
        <div class="flex flex-wrap gap-2">
            <x-ui.button :href="route('admin.program.show', $program)" size="sm" outlined>Detail</x-ui.button>
            <x-ui.button :href="route('admin.program.edit', $program)" size="sm" color="gray" outlined>Edit</x-ui.button>
        </div>
    </td>
</tr>
