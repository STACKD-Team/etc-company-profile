<tr>
    <td class="py-4 pr-4 font-heading font-bold text-etc-on-surface">
        <a href="{{ route('admin.placement-tests.show', $item) }}" class="text-etc-magenta hover:text-etc-primary">{{ $item->registration_code }}</a>
    </td>
    <td class="py-4 pr-4">{{ $item->applicant_name }}</td>
    <td class="py-4 pr-4 text-etc-on-muted">{{ $item->program?->name ?? '-' }}</td>
    <td class="py-4 pr-4"><x-ui.badge :status="$item->status" /></td>
    <td class="py-4 pr-4 text-etc-on-muted">{{ $item->placement_test_at?->format('d M Y H:i') ?? '-' }}</td>
</tr>
