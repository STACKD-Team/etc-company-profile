@php($reportCard = $item->reportCard)
<tr>
    <td class="py-4 pr-4">
        <p class="font-heading font-bold text-etc-on-surface">{{ $item->user?->full_name ?? $item->user?->name ?? '-' }}</p>
        <p class="mt-1 text-xs text-etc-on-muted">{{ $item->user?->email ?? '-' }}</p>
    </td>
    <td class="py-4 pr-4 text-etc-on-muted">{{ $item->courseClass?->name ?? '-' }}</td>
    <td class="py-4 pr-4 font-heading font-bold">{{ $reportCard?->total_score ?? '-' }}</td>
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
        <x-ui.button
            :href="$reportCard
                ? route($reportCard->is_published ? 'instructor.report-cards.show' : 'instructor.report-cards.edit', $reportCard)
                : route('instructor.report-cards.create', $item)"
            size="sm"
            outlined
        >
            {{ $reportCard ? ($reportCard->is_published ? 'Lihat' : 'Edit') : 'Mulai' }}
        </x-ui.button>
    </td>
</tr>
