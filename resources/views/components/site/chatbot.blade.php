@if (\Illuminate\Support\Facades\Route::has('public.chatbot.messages.store'))
    <section
        class="fixed bottom-7 right-7 z-50 md:bottom-8 md:right-8"
        data-chatbot-widget
        data-chatbot-endpoint="{{ route('public.chatbot.messages.store') }}"
        aria-label="Chatbot ETC Planet"
    >
        <div class="hidden w-[min(22rem,calc(100vw-2rem))] overflow-hidden rounded-card border border-etc-outline-variant bg-white text-etc-on-surface shadow-panel" data-chatbot-panel>
            <div class="flex items-center justify-between bg-etc-charcoal px-4 py-3 text-white">
                <div>
                    <p class="font-heading text-sm font-bold">ETC Planet Assistant</p>
                    <p class="text-xs text-white/70">Program, biaya, jadwal, dan pendaftaran</p>
                </div>
                <button type="button" class="flex h-9 w-9 items-center justify-center rounded-full text-white/80 hover:bg-white/10 hover:text-white" data-chatbot-close aria-label="Tutup chatbot">
                    <span class="material-symbols-outlined text-lg">close</span>
                </button>
            </div>

            <div class="max-h-80 space-y-3 overflow-y-auto bg-etc-surface px-4 py-4" data-chatbot-messages>
                <p class="max-w-[85%] rounded-2xl rounded-tl-sm bg-white px-4 py-3 text-sm leading-6 text-etc-on-muted shadow-soft">
                    Halo! Aku bisa bantu jawab tentang program, biaya, jadwal, pendaftaran, placement test, dan kontak ETC Planet.
                </p>
            </div>

            <form class="flex gap-2 border-t border-etc-outline-variant bg-white p-3" data-chatbot-form>
                <label for="public-chatbot-message" class="sr-only">Pesan chatbot</label>
                <input
                    id="public-chatbot-message"
                    name="message"
                    type="text"
                    maxlength="1000"
                    required
                    autocomplete="off"
                    class="min-h-11 min-w-0 flex-1 rounded-full border border-etc-outline-variant px-4 text-sm outline-none focus:border-etc-magenta focus:ring-4 focus:ring-etc-magenta/10"
                    placeholder="Tanya program ETC..."
                >
                <button type="submit" class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-etc-magenta text-white hover:bg-etc-primary" aria-label="Kirim pesan">
                    <span class="material-symbols-outlined text-lg">send</span>
                </button>
            </form>
        </div>

        <button type="button" class="mt-3 inline-flex h-14 w-14 items-center justify-center rounded-full bg-etc-magenta text-white shadow-[0_12px_28px_rgba(230,0,127,0.35)] transition hover:bg-etc-primary" data-chatbot-toggle aria-expanded="false" aria-label="Buka chatbot">
            <span class="material-symbols-outlined text-2xl">smart_toy</span>
        </button>
    </section>
@endif
