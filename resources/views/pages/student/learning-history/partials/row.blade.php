@php
    $class = $item->courseClass;
    $program = $class?->program;
    $instructor = $class?->instructor;
    $publishedReport = $item->reportCard?->is_published ? $item->reportCard : null;
    $statusLabel = $statusLabels[$item->status] ?? str($item->status)->headline();
@endphp

<tr class="group">
    <td class="py-4 pr-4">
        <p class="font-heading font-bold text-etc-on-surface">{{ $class?->name ?? 'Kelas ETC' }}</p>
        <p class="mt-1 text-xs text-etc-on-muted">{{ $class?->schedule_days ?? '-' }} {{ $class?->schedule_time ? '- '.$class->schedule_time : '' }}</p>
    </td>
    <td class="py-4 pr-4 text-etc-on-muted">{{ $program?->name ?? '-' }}</td>
    <td class="py-4 pr-4">
        <span class="block">{{ $item->enrolled_at?->format('d M Y') ?? '-' }}</span>
        <span class="mt-1 block text-xs text-etc-on-muted">{{ $item->completed_at?->format('d M Y') ?? 'Masih berjalan' }}</span>
    </td>
    <td class="py-4 pr-4 text-etc-on-muted">{{ $instructor?->full_name ?? $instructor?->name ?? 'Belum ditentukan' }}</td>
    <td class="py-4 pr-4">
        <div class="flex flex-wrap gap-2">
            <x-ui.badge :status="$item->status">{{ $statusLabel }}</x-ui.badge>
            @if ($publishedReport)
                <x-ui.badge status="published">Rapor Published</x-ui.badge>
            @else
                <x-ui.badge status="unpublished">Rapor Belum Terbit</x-ui.badge>
            @endif
        </div>
    </td>
    <td class="py-4 pr-4">
        <div class="flex items-center gap-2">
            @if ($class)
                <x-ui.icon-button
                    :href="route('student.classes.show', $class)"
                    icon="heroicon-m-eye"
                    label="Lihat detail kelas"
                    size="sm"
                    outlined
                />
            @endif
            @if ($publishedReport)
                <x-ui.icon-button
                    :href="route('student.report-cards.show', $publishedReport)"
                    icon="heroicon-m-document-text"
                    label="Lihat rapor"
                    size="sm"
                    outlined
                />
            @endif
        </div>
    </td>
</tr>
