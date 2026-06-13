@php($log = $item)

<tr>
    <td class="py-4 pr-4 text-etc-on-muted">{{ $log->created_at?->format('d M Y H:i') ?? '-' }}</td>
    <td class="py-4 pr-4">
        <p class="max-w-[160px] truncate font-heading text-xs font-bold text-etc-on-surface">{{ $log->session_id }}</p>
        <p class="mt-1 text-xs text-etc-on-muted">{{ $log->user?->full_name ?? $log->user?->name ?? 'Guest' }}</p>
    </td>
    <td class="py-4 pr-4"><x-ui.badge status="primary">{{ ucfirst($log->intent ?: 'general') }}</x-ui.badge></td>
    <td class="py-4 pr-4">
        <p class="line-clamp-2 text-sm text-etc-on-surface">{{ $log->user_message }}</p>
        <p class="mt-2 line-clamp-2 text-xs leading-5 text-etc-on-muted">{{ $log->bot_response }}</p>
    </td>
    <td class="py-4 pr-4">
        @if ($log->is_helpful === null)
            <x-ui.badge status="ignored">Belum ada</x-ui.badge>
        @else
            <x-ui.badge :status="$log->is_helpful ? 'success' : 'failed'">{{ $log->is_helpful ? 'Helpful' : 'Not helpful' }}</x-ui.badge>
        @endif
    </td>
    <td class="py-4 pr-4">
        <x-ui.button :href="route('admin.chatbot-log.show', $log)" size="sm" outlined>Detail</x-ui.button>
    </td>
</tr>
