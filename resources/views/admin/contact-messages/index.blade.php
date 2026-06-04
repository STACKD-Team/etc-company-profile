<x-layouts.dashboard title="Pesan Kontak" area="admin" active="contact_messages">
    <section class="space-y-5">
        <form method="GET" class="rounded-card bg-white p-5 shadow-panel">
            <div class="grid gap-3 lg:grid-cols-[1fr_170px_auto]">
                <input name="search" value="{{ request('search') }}" placeholder="Cari nama, email, atau subjek" class="min-h-11 rounded-xl border border-etc-outline-variant px-4 text-sm focus:border-etc-magenta focus:outline-none">
                <select name="is_read" class="min-h-11 rounded-xl border border-etc-outline-variant px-4 text-sm focus:border-etc-magenta focus:outline-none">
                    <option value="">Semua status</option>
                    <option value="0" @selected(request('is_read') === '0')>Belum dibaca</option>
                    <option value="1" @selected(request('is_read') === '1')>Sudah dibaca</option>
                </select>
                <button class="inline-flex min-h-11 items-center justify-center gap-2 rounded-pill bg-etc-charcoal px-5 font-heading text-sm font-bold text-white">
                    <span class="material-symbols-outlined text-base">search</span>
                    Filter
                </button>
            </div>
        </form>

        <div class="overflow-hidden rounded-card bg-white shadow-panel">
            <div class="grid border-b border-etc-outline-variant bg-etc-surface-container px-5 py-3 font-heading text-xs font-black uppercase text-etc-on-muted md:grid-cols-[1.2fr_1fr_140px_120px]">
                <span>Pengirim</span>
                <span>Subjek</span>
                <span>Tanggal</span>
                <span>Status</span>
            </div>
            @forelse ($messages as $message)
                <a href="{{ route('admin.contact-messages.show', $message) }}" class="grid gap-3 border-b border-etc-outline-variant px-5 py-4 transition hover:bg-etc-surface-container md:grid-cols-[1.2fr_1fr_140px_120px] md:items-center">
                    <div class="min-w-0">
                        <p class="truncate font-heading text-sm font-black text-etc-on-surface">{{ $message->name }}</p>
                        <p class="mt-1 truncate text-xs text-etc-on-muted">{{ $message->email }}{{ $message->phone ? ' · '.$message->phone : '' }}</p>
                    </div>
                    <div class="min-w-0">
                        <p class="truncate text-sm font-bold text-etc-on-surface">{{ $message->subject ?: 'Tanpa subjek' }}</p>
                        <p class="mt-1 line-clamp-1 text-xs text-etc-on-muted">{{ $message->message }}</p>
                    </div>
                    <span class="text-sm text-etc-on-muted">{{ $message->created_at?->format('d M Y') }}</span>
                    <span @class([
                        'inline-flex w-fit rounded-full px-3 py-1 font-heading text-[11px] font-black uppercase',
                        'bg-green-50 text-green-700' => $message->is_read,
                        'bg-etc-magenta/10 text-etc-magenta' => ! $message->is_read,
                    ])>{{ $message->is_read ? 'Dibaca' : 'Baru' }}</span>
                </a>
            @empty
                <div class="p-10 text-center">
                    <span class="material-symbols-outlined text-5xl text-etc-magenta">inbox</span>
                    <h2 class="mt-4 font-heading text-xl font-black">Tidak ada pesan</h2>
                </div>
            @endforelse
        </div>

        <div>{{ $messages->links() }}</div>
    </section>
</x-layouts.dashboard>
