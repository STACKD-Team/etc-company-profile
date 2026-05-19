<tr>
    <td class="py-4 pr-4 font-heading font-bold text-etc-on-surface">{{ $item->user?->full_name ?? $item->user?->name ?? '-' }}</td>
    <td class="py-4 pr-4 text-etc-on-muted">{{ $item->user?->email ?? '-' }}</td>
    <td class="py-4 pr-4">{{ $item->courseClass?->name ?? '-' }}</td>
    <td class="py-4 pr-4">{{ $item->status ?? '-' }}</td>
</tr>
