<x-layouts.dashboard title="Chatbot Logs" area="admin" active="chatbot_logs">
    <x-ui.data-table
        :items="$logs"
        :columns="[
            'created_at' => ['label' => 'Waktu', 'sortable' => true],
            'session_id' => ['label' => 'Session', 'sortable' => true],
            'intent' => ['label' => 'Intent', 'sortable' => true, 'filter' => ['type' => 'select', 'name' => 'intent', 'options' => collect($intents)->mapWithKeys(fn ($intent) => [$intent => ucfirst($intent)])->all()]],
            'message' => 'Percakapan',
            'is_helpful' => ['label' => 'Feedback', 'sortable' => true, 'filter' => ['type' => 'select', 'name' => 'is_helpful', 'options' => ['1' => 'Helpful', '0' => 'Not helpful']]],
        ]"
        row-view="admin.chatbot-logs.partials.row"
        empty="Log belum tersedia"
        empty-description="Percakapan chatbot akan tercatat setelah pengguna mengirim pesan."
        search-name="session_id"
        search-placeholder="Cari session ID"
    />
</x-layouts.dashboard>
