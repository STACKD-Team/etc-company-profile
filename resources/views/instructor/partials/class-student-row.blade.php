@php($reportCard = $item->reportCard)
<tr>
    <td class="py-4 pr-4">
        <p class="font-heading font-bold text-etc-on-surface">{{ $item->user?->full_name ?? $item->user?->name ?? '-' }}</p>
        <p class="mt-1 text-xs text-etc-on-muted">{{ $item->user?->email ?? '-' }}</p>
    </td>
    <td class="py-4 pr-4"><x-ui.badge :status="$item->status" /></td>
    <td class="py-4 pr-4">
        <x-ui.badge :status="$item->assessment_state">
            {{ match ($item->assessment_state) {
                'not_started' => 'Belum mulai',
                'incomplete' => 'Belum lengkap',
                'complete' => 'Lengkap',
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
            {{ $reportCard ? ($reportCard->is_published ? 'Lihat' : 'Nilai') : 'Mulai' }}
        </x-ui.button>
    </td>
</tr>
