<x-layouts.dashboard title="Detail Chatbot Log" area="admin" active="chatbot_logs">
    <x-ui.resource-header
        title="Detail Chatbot Log"
        :subtitle="$log->session_id"
        :back-url="route('admin.chatbot-log.index')"
    >
        <x-slot:status>
            <x-ui.badge status="primary">{{ ucfirst($log->intent ?: 'general') }}</x-ui.badge>
        </x-slot:status>
    </x-ui.resource-header>

    <div class="grid gap-6 lg:grid-cols-[320px_minmax(0,1fr)]">
        <x-ui.detail-card heading="Metadata">
            <x-ui.description-list columns="1">
                <x-ui.description-item label="Waktu" :value="$log->created_at?->format('d M Y H:i') ?: '-'" />
                <x-ui.description-item label="Session" :value="$log->session_id" />
                <x-ui.description-item label="User" :value="$log->user?->full_name ?? $log->user?->name ?? 'Guest'" />
                <x-ui.description-item label="Intent"><x-ui.badge status="primary">{{ ucfirst($log->intent ?: 'general') }}</x-ui.badge></x-ui.description-item>
                <x-ui.description-item label="Feedback">
                    @if ($log->is_helpful === null)
                        <x-ui.badge status="ignored">Belum ada</x-ui.badge>
                    @else
                        <x-ui.badge :status="$log->is_helpful ? 'success' : 'failed'">{{ $log->is_helpful ? 'Helpful' : 'Not helpful' }}</x-ui.badge>
                    @endif
                </x-ui.description-item>
            </x-ui.description-list>
        </x-ui.detail-card>

        <x-ui.detail-card heading="Percakapan">
            <div class="space-y-5">
                <div>
                    <h2 class="font-heading text-sm font-bold text-etc-on-surface">Pesan User</h2>
                    <p class="mt-2 rounded-box bg-etc-surface-container p-4 text-sm leading-6 text-etc-on-surface">{{ $log->user_message }}</p>
                </div>
                <div>
                    <h2 class="font-heading text-sm font-bold text-etc-on-surface">Respons Bot</h2>
                    <p class="mt-2 rounded-box bg-etc-surface-container p-4 text-sm leading-6 text-etc-on-surface">{{ $log->bot_response }}</p>
                </div>
            </div>
        </x-ui.detail-card>
    </div>
</x-layouts.dashboard>
