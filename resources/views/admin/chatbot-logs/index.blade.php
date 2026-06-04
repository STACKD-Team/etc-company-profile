<x-layouts.dashboard title="Chatbot Logs" area="admin" active="chatbot_logs">
    <section class="space-y-5">
        <form method="GET" class="rounded-card bg-white p-5 shadow-panel">
            <div class="grid gap-3 xl:grid-cols-[1fr_170px_150px_150px_150px_auto]">
                <input name="session_id" value="{{ request('session_id') }}" placeholder="Session ID" class="min-h-11 rounded-xl border border-etc-outline-variant px-4 text-sm focus:border-etc-magenta focus:outline-none">
                <select name="intent" class="min-h-11 rounded-xl border border-etc-outline-variant px-4 text-sm focus:border-etc-magenta focus:outline-none">
                    <option value="">Semua intent</option>
                    @foreach ($intents as $intent)
                        <option value="{{ $intent }}" @selected(request('intent') === $intent)>{{ ucfirst($intent) }}</option>
                    @endforeach
                </select>
                <select name="is_helpful" class="min-h-11 rounded-xl border border-etc-outline-variant px-4 text-sm focus:border-etc-magenta focus:outline-none">
                    <option value="">Semua feedback</option>
                    <option value="1" @selected(request('is_helpful') === '1')>Helpful</option>
                    <option value="0" @selected(request('is_helpful') === '0')>Not helpful</option>
                </select>
                <input type="date" name="created_from" value="{{ request('created_from') }}" class="min-h-11 rounded-xl border border-etc-outline-variant px-4 text-sm focus:border-etc-magenta focus:outline-none">
                <input type="date" name="created_to" value="{{ request('created_to') }}" class="min-h-11 rounded-xl border border-etc-outline-variant px-4 text-sm focus:border-etc-magenta focus:outline-none">
                <button class="inline-flex min-h-11 items-center justify-center gap-2 rounded-pill bg-etc-charcoal px-5 font-heading text-sm font-bold text-white">
                    <span class="material-symbols-outlined text-base">search</span>
                    Filter
                </button>
            </div>
        </form>

        <div class="overflow-hidden rounded-card bg-white shadow-panel">
            <div class="hidden border-b border-etc-outline-variant bg-etc-surface-container px-5 py-3 font-heading text-xs font-black uppercase text-etc-on-muted lg:grid lg:grid-cols-[150px_130px_1fr_1fr_110px]">
                <span>Waktu</span>
                <span>Intent</span>
                <span>Pesan User</span>
                <span>Jawaban Bot</span>
                <span>Feedback</span>
            </div>
            @forelse ($logs as $log)
                <article class="grid gap-3 border-b border-etc-outline-variant px-5 py-4 lg:grid-cols-[150px_130px_1fr_1fr_110px]">
                    <span class="text-sm font-bold text-etc-on-muted">{{ $log->created_at?->format('d M H:i') }}</span>
                    <span class="h-fit w-fit rounded-full bg-etc-magenta/10 px-3 py-1 font-heading text-[11px] font-black uppercase text-etc-magenta">{{ $log->intent ?: 'general' }}</span>
                    <p class="text-sm leading-6 text-etc-on-surface">{{ $log->user_message }}</p>
                    <p class="text-sm leading-6 text-etc-on-muted">{{ $log->bot_response }}</p>
                    <span class="text-sm font-bold text-etc-on-muted">
                        @if ($log->is_helpful === null)
                            -
                        @else
                            {{ $log->is_helpful ? 'Helpful' : 'Not helpful' }}
                        @endif
                    </span>
                </article>
            @empty
                <div class="p-10 text-center">
                    <span class="material-symbols-outlined text-5xl text-etc-magenta">forum</span>
                    <h2 class="mt-4 font-heading text-xl font-black">Log belum tersedia</h2>
                </div>
            @endforelse
        </div>

        <div>{{ $logs->links() }}</div>
    </section>
</x-layouts.dashboard>
