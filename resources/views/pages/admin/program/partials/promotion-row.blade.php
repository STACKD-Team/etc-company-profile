@php($promotion = $item)

<tr>
    <td class="py-4 pr-4">
        <p class="font-heading font-bold text-etc-on-surface">{{ $promotion->title }}</p>
        <p class="mt-1 text-xs text-etc-on-muted">{{ $promotion->badge_label ?: '-' }}</p>
    </td>
    <td class="py-4 pr-4 text-etc-on-muted">
        {{ $promotion->discount_type === 'percentage' ? rtrim(rtrim(number_format((float) $promotion->discount_value, 2), '0'), '.').'%' : 'Rp '.number_format((float) $promotion->discount_value, 0, ',', '.') }}
    </td>
    <td class="py-4 pr-4 text-etc-on-muted">
        {{ $promotion->starts_at?->format('d M Y') ?? '-' }} - {{ $promotion->ends_at?->format('d M Y') ?? '-' }}
    </td>
    <td class="py-4 pr-4"><x-ui.badge :status="$promotion->is_active ? 'active' : 'inactive'">{{ $promotion->is_active ? 'Aktif' : 'Nonaktif' }}</x-ui.badge></td>
</tr>
