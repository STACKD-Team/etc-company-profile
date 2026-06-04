<x-layouts.dashboard title="Detail Pesan" area="admin" active="contact_messages">
    <x-slot:headerActions>
        <a href="{{ route('admin.contact-messages.index') }}" class="inline-flex min-h-11 items-center gap-2 rounded-pill border border-etc-outline-variant px-5 font-heading text-sm font-bold text-etc-charcoal">
            <span class="material-symbols-outlined text-base">arrow_back</span>
            Kembali
        </a>
    </x-slot:headerActions>

    <article class="grid gap-5 lg:grid-cols-[1fr_320px]">
        <div class="rounded-card bg-white p-6 shadow-panel">
            <div class="flex flex-wrap items-center gap-2">
                <span class="rounded-full bg-green-50 px-3 py-1 font-heading text-[11px] font-black uppercase text-green-700">Dibaca</span>
                @if ($message->replied_at)
                    <span class="rounded-full bg-etc-surface-container px-3 py-1 font-heading text-[11px] font-black uppercase text-etc-on-muted">Dibalas {{ $message->replied_at->format('d M Y') }}</span>
                @endif
            </div>
            <h2 class="mt-5 font-heading text-2xl font-black text-etc-on-surface">{{ $message->subject ?: 'Tanpa subjek' }}</h2>
            <p class="mt-6 whitespace-pre-line text-[15px] leading-8 text-etc-on-muted">{{ $message->message }}</p>
        </div>

        <aside class="rounded-card bg-etc-charcoal p-6 text-white shadow-panel">
            <p class="font-heading text-sm font-black uppercase text-etc-magenta">Pengirim</p>
            <h3 class="mt-4 font-heading text-xl font-black">{{ $message->name }}</h3>
            <div class="mt-5 space-y-4 text-sm text-white/70">
                <p class="flex gap-3"><span class="material-symbols-outlined text-base text-etc-magenta">mail</span>{{ $message->email }}</p>
                <p class="flex gap-3"><span class="material-symbols-outlined text-base text-etc-magenta">call</span>{{ $message->phone ?: '-' }}</p>
                <p class="flex gap-3"><span class="material-symbols-outlined text-base text-etc-magenta">schedule</span>{{ $message->created_at?->format('d M Y H:i') }}</p>
            </div>
        </aside>
    </article>
</x-layouts.dashboard>
