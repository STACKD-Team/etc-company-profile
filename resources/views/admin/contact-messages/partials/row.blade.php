@php($message = $item)

<tr>
    <td class="py-4 pr-4">
        <p class="font-heading font-bold text-etc-on-surface">{{ $message->name }}</p>
        <p class="mt-1 text-xs text-etc-on-muted">{{ $message->email }}{{ $message->phone ? ' - '.$message->phone : '' }}</p>
    </td>
    <td class="py-4 pr-4">
        <p class="font-heading font-bold text-etc-on-surface">{{ $message->subject ?: 'Tanpa subjek' }}</p>
        <p class="mt-1 line-clamp-1 text-xs text-etc-on-muted">{{ $message->message }}</p>
    </td>
    <td class="py-4 pr-4"><x-ui.badge :status="$message->is_read ? 'processed' : 'pending'">{{ $message->is_read ? 'Dibaca' : 'Baru' }}</x-ui.badge></td>
    <td class="py-4 pr-4 text-etc-on-muted">{{ $message->created_at?->format('d M Y H:i') ?? '-' }}</td>
    <td class="py-4 pr-4">
        <x-ui.button :href="route('admin.contact-messages.show', $message)" size="sm" outlined>Detail</x-ui.button>
    </td>
</tr>
