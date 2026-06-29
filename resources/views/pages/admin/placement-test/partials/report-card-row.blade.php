<tr>
    <td class="py-4 pr-4 font-heading font-bold text-etc-on-surface">
        <a href="{{ route('admin.report-card.show', $item) }}" class="text-etc-magenta hover:text-etc-primary">
            {{ $item->enrollment?->user?->full_name ?? $item->enrollment?->user?->name ?? '-' }}
        </a>
    </td>
    <td class="py-4 pr-4 text-etc-on-muted">{{ $item->enrollment?->courseClass?->name ?? '-' }}</td>
    <td class="py-4 pr-4">{{ $item->total_score ?? '-' }}</td>
    <td class="py-4 pr-4"><x-ui.badge :status="$item->is_published ? 'published' : 'draft'">{{ $item->is_published ? 'Published' : 'Draft' }}</x-ui.badge></td>
    <td class="py-4 pr-4 text-etc-on-muted">{{ $item->issued_at?->format('d M Y') ?? '-' }}</td>
    <td class="py-4 pr-4">
        <div class="flex flex-wrap gap-2">
            <x-ui.button :href="route('admin.report-card.show', $item)" size="sm" outlined>Detail</x-ui.button>
            <x-ui.button :href="route('admin.report-card.edit', $item)" size="sm" color="gray" outlined>Edit</x-ui.button>
        </div>
    </td>
</tr>
