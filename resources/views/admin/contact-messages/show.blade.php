<x-layouts.dashboard title="Detail Pesan" area="admin" active="contact_messages">
    <x-slot:headerActions>
        <x-ui.button :href="route('admin.contact-messages.index')" outlined color="gray" icon="heroicon-m-arrow-left">
            Kembali
        </x-ui.button>
    </x-slot:headerActions>

    <div class="grid gap-5 lg:grid-cols-[minmax(0,1fr)_320px]">
        <x-ui.panel>
            <div class="flex flex-wrap items-center gap-2">
                <x-ui.badge status="success" size="sm">Dibaca</x-ui.badge>
                @if ($message->replied_at)
                    <x-ui.badge status="ignored" size="sm">Dibalas {{ $message->replied_at->format('d M Y') }}</x-ui.badge>
                @endif
            </div>
            <h2 class="mt-5 font-heading text-2xl font-black text-etc-on-surface">{{ $message->subject ?: 'Tanpa subjek' }}</h2>
            <p class="mt-6 whitespace-pre-line text-[15px] leading-8 text-etc-on-muted">{{ $message->message }}</p>
        </x-ui.panel>

        <x-ui.panel heading="Pengirim">
            <h3 class="font-heading text-xl font-black text-etc-on-surface">{{ $message->name }}</h3>
            <div class="mt-5 space-y-4 text-sm text-etc-on-muted">
                <p class="flex gap-3"><span class="material-symbols-outlined text-base text-etc-magenta">mail</span>{{ $message->email }}</p>
                <p class="flex gap-3"><span class="material-symbols-outlined text-base text-etc-magenta">call</span>{{ $message->phone ?: '-' }}</p>
                <p class="flex gap-3"><span class="material-symbols-outlined text-base text-etc-magenta">schedule</span>{{ $message->created_at?->format('d M Y H:i') }}</p>
            </div>
        </x-ui.panel>
    </div>
</x-layouts.dashboard>
