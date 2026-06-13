@php($student = $item)

<tr>
    <td class="py-4 pr-4">
        <p class="font-heading font-bold text-etc-on-surface">{{ $student->full_name ?? $student->name }}</p>
        <p class="mt-1 text-xs text-etc-on-muted">{{ $student->no_induk ?: 'No induk belum diisi' }}</p>
    </td>
    <td class="py-4 pr-4 text-etc-on-muted">{{ $student->email }}</td>
    <td class="py-4 pr-4">
        <x-ui.badge :status="$student->status ?? ($student->is_active ? 'active' : 'inactive')">
            {{ $student->status ?: ($student->is_active ? 'Aktif' : 'Nonaktif') }}
        </x-ui.badge>
    </td>
    <td class="py-4 pr-4 font-heading font-bold text-etc-on-surface">{{ number_format((int) $student->enrollments_count) }}</td>
    <td class="py-4 pr-4 text-etc-on-muted">{{ $student->created_at?->format('d M Y') ?? '-' }}</td>
    <td class="py-4 pr-4">
        <x-ui.button :href="route('admin.student.show', $student)" size="sm" outlined>Detail</x-ui.button>
    </td>
</tr>
