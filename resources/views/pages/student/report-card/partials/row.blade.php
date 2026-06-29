@php
    $class = $item->enrollment?->courseClass;
    $program = $class?->program;
@endphp

<tr class="group">
    <td class="py-4 pr-4">
        <div class="flex flex-wrap items-center gap-2">
            <x-ui.badge status="published">Published</x-ui.badge>
            <x-ui.badge status="success">Nilai {{ $item->final_grade ?? '-' }}</x-ui.badge>
        </div>
        <p class="mt-2 font-heading font-bold text-etc-on-surface">{{ $item->issued_at?->format('d M Y') ?? 'Tanggal belum tersedia' }}</p>
    </td>
    <td class="py-4 pr-4 text-etc-on-muted">{{ $program?->name ?? 'Program ETC Planet' }}</td>
    <td class="py-4 pr-4 text-etc-on-muted">
        <span class="block">{{ $class?->name ?? 'Kelas ETC' }}</span>
        <span class="mt-1 block text-xs">{{ $class?->instructor?->full_name ?? $class?->instructor?->name ?? 'Instruktur belum ditentukan' }}</span>
        <span class="mt-1 block text-xs font-semibold">Ruangan: {{ $class?->room_label ?? 'Belum ditentukan' }}</span>
    </td>
    <td class="py-4 pr-4">{{ $item->issued_at?->format('d M Y') ?? '-' }}</td>
    <td class="py-4 pr-4">
        <p class="font-heading font-bold text-etc-on-surface">{{ $item->final_grade ?? '-' }}</p>
        <p class="mt-1 text-xs text-etc-on-muted">Total {{ $item->total_score ?? '-' }}</p>
    </td>
    <td class="py-4 pr-4">
        @if ($item->pdf_path)
            <x-ui.badge status="with_file">File tersedia</x-ui.badge>
        @else
            <x-ui.badge status="without_file">Belum ada file</x-ui.badge>
        @endif
    </td>
    <td class="py-4 pr-4">
        <div class="flex items-center gap-2">
            <x-ui.icon-button
                :href="route('student.report-cards.show', $item)"
                icon="heroicon-m-eye"
                label="Lihat rapor"
                size="sm"
                outlined
            />
            @if ($item->pdf_path)
                <x-ui.icon-button
                    :href="route('student.report-cards.download', $item)"
                    icon="heroicon-m-arrow-down-tray"
                    label="Unduh rapor"
                    size="sm"
                    outlined
                />
            @endif
        </div>
    </td>
</tr>
