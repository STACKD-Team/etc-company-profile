<tr class="group">
    <td class="py-4 pr-4">
        <p class="font-heading font-bold text-etc-on-surface">{{ $item->user?->full_name ?? $item->user?->name ?? '-' }}</p>
        <p class="mt-1 text-xs text-etc-on-muted">{{ $item->user?->email ?? '-' }}</p>
    </td>
    <td class="py-4 pr-4 text-etc-on-muted">
        <a
            href="{{ route('instructor.classes.show', $item->courseClass) }}"
            class="rounded-selector font-semibold text-etc-magenta outline-none hover:text-etc-primary focus-visible:ring-2 focus-visible:ring-etc-magenta focus-visible:ring-offset-2"
        >
            {{ $item->courseClass?->name ?? '-' }}
        </a>
    </td>
    <td class="py-4 pr-4">{{ $item->enrolled_at?->format('d M Y') ?? '-' }}</td>
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
        @if ($item->reportCard && ! $item->can_edit_assessment)
            <x-ui.icon-button
                :href="route('instructor.report-cards.show', $item->reportCard)"
                icon="heroicon-m-eye"
                label="Lihat assessment siswa"
                size="sm"
                outlined
            />
        @else
            <x-ui.icon-button
                :href="$item->reportCard
                    ? route('instructor.report-cards.edit', $item->reportCard)
                    : route('instructor.report-cards.create', $item)"
                icon="heroicon-m-pencil-square"
                :label="$item->reportCard ? 'Edit assessment siswa' : 'Mulai assessment siswa'"
                size="sm"
                outlined
            />
        @endif
    </td>
</tr>
