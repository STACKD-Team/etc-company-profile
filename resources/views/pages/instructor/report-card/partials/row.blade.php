@php($reportCard = $item->reportCard)
<tr class="group">
    <td class="py-4 pr-4">
        <p class="font-heading font-bold text-etc-on-surface">{{ $item->user?->full_name ?? $item->user?->name ?? '-' }}</p>
        <p class="mt-1 text-xs text-etc-on-muted">{{ $item->user?->email ?? '-' }}</p>
    </td>
    <td class="py-4 pr-4 text-etc-on-muted">{{ $item->courseClass?->name ?? '-' }}</td>
    <td class="py-4 pr-4 font-heading font-bold">
        {{ $reportCard?->total_score !== null ? $reportCard->total_score.'/100' : '-' }}
    </td>
    <td class="py-4 pr-4">
        <x-ui.badge :status="$item->assessment_state">
            {{ match ($item->assessment_state) {
                'not_started' => 'Belum mulai',
                'incomplete' => 'Belum lengkap',
                'complete' => 'Draft lengkap',
                'published' => 'Published',
                default => str($item->assessment_state)->headline(),
            } }}
        </x-ui.badge>
    </td>
    <td class="py-4 pr-4">
        @if ($reportCard && ! $item->can_edit_assessment)
            <x-ui.icon-button
                :href="route('instructor.report-cards.show', $reportCard)"
                icon="heroicon-m-eye"
                label="Lihat assessment siswa"
                size="sm"
                outlined
            />
        @else
            <x-ui.icon-button
                :href="$reportCard
                    ? route('instructor.report-cards.edit', $reportCard)
                    : route('instructor.report-cards.create', $item)"
                icon="heroicon-m-pencil-square"
                :label="$reportCard ? 'Edit assessment siswa' : 'Mulai assessment siswa'"
                size="sm"
                outlined
            />
        @endif
    </td>
</tr>
