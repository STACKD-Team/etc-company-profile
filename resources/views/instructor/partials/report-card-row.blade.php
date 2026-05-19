<tr>
    <td class="py-4 pr-4 font-heading font-bold text-etc-on-surface">{{ $item->enrollment?->user?->full_name ?? $item->enrollment?->user?->name ?? '-' }}</td>
    <td class="py-4 pr-4 text-etc-on-muted">{{ $item->enrollment?->courseClass?->name ?? '-' }}</td>
    <td class="py-4 pr-4">{{ $item->total_score ?? '-' }}</td>
    <td class="py-4 pr-4">{{ $item->is_published ? 'Published' : 'Draft' }}</td>
</tr>
