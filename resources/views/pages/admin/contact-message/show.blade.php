<x-layouts.dashboard title="Detail Pesan" area="admin" active="contact_messages">
    <x-ui.resource-header
        :title="$message->subject ?: 'Tanpa subjek'"
        :subtitle="$message->name.' - '.$message->created_at?->format('d M Y H:i')"
        :back-url="route('admin.contact-message.index')"
    >
        <x-slot:status>
            <x-ui.badge status="success">Dibaca</x-ui.badge>
            @if ($message->replied_at)
                <x-ui.badge status="ignored">Dibalas {{ $message->replied_at->format('d M Y') }}</x-ui.badge>
            @endif
        </x-slot:status>
    </x-ui.resource-header>

    <div class="grid gap-5 lg:grid-cols-[minmax(0,1fr)_320px]">
        <x-ui.detail-card heading="Pesan">
            <p class="whitespace-pre-line text-[15px] leading-8 text-etc-on-muted">{{ $message->message }}</p>
        </x-ui.detail-card>

        <x-ui.detail-card heading="Pengirim">
            <x-ui.description-list columns="1">
                <x-ui.description-item label="Nama" :value="$message->name" />
                <x-ui.description-item label="Email" :value="$message->email" />
                <x-ui.description-item label="No HP" :value="$message->phone ?: '-'" />
                <x-ui.description-item label="Dikirim" :value="$message->created_at?->format('d M Y H:i') ?: '-'" />
            </x-ui.description-list>
        </x-ui.detail-card>
    </div>
</x-layouts.dashboard>
