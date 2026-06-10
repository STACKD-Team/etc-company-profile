<tr>
    <td class="py-4 pr-4">
        <p class="font-heading font-bold text-etc-on-surface">{{ $item->user?->full_name ?? $item->user?->name ?? '-' }}</p>
        <p class="mt-1 text-xs text-etc-on-muted">{{ $item->user?->email ?? '-' }}</p>
    </td>
    <td class="py-4 pr-4 text-etc-on-muted">
        <a href="{{ route('instructor.classes.show', $item->courseClass) }}" class="font-semibold text-etc-magenta hover:text-etc-primary">
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
        @if ($item->reportCard)
            <x-ui.button
                :href="route($item->reportCard->is_published ? 'instructor.report-cards.show' : 'instructor.report-cards.edit', $item->reportCard)"
                size="sm"
                outlined
            >
                {{ $item->reportCard->is_published ? 'Lihat' : 'Nilai' }}
            </x-ui.button>
        @else
            <x-ui.button :href="route('instructor.report-cards.create', $item)" size="sm" outlined>
                Mulai
            </x-ui.button>
        @endif
    </td>
</tr>
