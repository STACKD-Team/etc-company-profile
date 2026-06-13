@php($instructor = $item)

<tr>
    <td class="py-4 pr-4">
        <p class="font-heading font-bold text-etc-on-surface">{{ $instructor->full_name ?? $instructor->name }}</p>
        <p class="mt-1 text-xs text-etc-on-muted">{{ $instructor->instructor_position ?: 'Instructor ETC Planet' }}</p>
    </td>
    <td class="py-4 pr-4 text-etc-on-muted">{{ $instructor->email }}</td>
    <td class="py-4 pr-4 text-etc-on-muted">{{ $instructor->instructor_specialization ?: '-' }}</td>
    <td class="py-4 pr-4 font-heading font-bold text-etc-on-surface">{{ number_format((int) $instructor->classes_taught_count) }}</td>
    <td class="py-4 pr-4 text-etc-on-muted">{{ $instructor->created_at?->format('d M Y') ?? '-' }}</td>
    <td class="py-4 pr-4">
        <x-ui.button :href="route('admin.instructor.show', $instructor)" size="sm" outlined>Detail</x-ui.button>
    </td>
</tr>
