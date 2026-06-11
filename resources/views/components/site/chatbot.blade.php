@if (\Illuminate\Support\Facades\Route::has('public.chatbot.messages.store'))
    <section
        class="fixed bottom-24 right-4 z-50 md:bottom-8 md:right-8"
        data-chatbot-widget
        data-chatbot-endpoint="{{ route('public.chatbot.messages.store') }}"
        aria-label="Chatbot ETC Planet"
    >
        <div class="hidden w-[min(22rem,calc(100vw-2rem))] overflow-hidden rounded-card border-2 border-etc-outline-variant bg-etc-surface text-etc-on-surface shadow-panel" data-chatbot-panel>
            <div class="flex items-center justify-between bg-etc-charcoal px-4 py-3 text-white">
                <div>
                    <p class="font-heading text-sm font-bold">ETC Planet Assistant</p>
                    <p class="text-xs text-white/70">Program, biaya, jadwal, dan pendaftaran</p>
                </div>
                <x-ui.icon-button
                    icon="heroicon-m-x-mark"
                    label="Tutup chatbot"
                    color="gray"
                    size="sm"
                    class="!rounded-pill !text-white/80 hover:!bg-etc-surface/10 hover:!text-white"
                    data-chatbot-close
                />
            </div>

            <div class="max-h-80 space-y-3 overflow-y-auto bg-etc-surface-low px-4 py-4" data-chatbot-messages>
                <p class="max-w-[85%] rounded-card bg-etc-surface px-4 py-3 text-sm leading-6 text-etc-on-muted shadow-soft">
                    Halo! Aku bisa bantu jawab tentang program, biaya, jadwal, pendaftaran, placement test, dan kontak ETC Planet.
                </p>
            </div>

            <form class="flex gap-2 border-t-2 border-etc-outline-variant bg-etc-surface p-3" data-chatbot-form>
                <x-ui.field
                    id="public-chatbot-message"
                    name="message"
                    label="Pesan chatbot"
                    label-class="sr-only"
                    wrapper-class="min-w-0 flex-1"
                    maxlength="1000"
                    required
                    autocomplete="off"
                    size="lg"
                    placeholder="Tanya program ETC..."
                />
                <x-ui.icon-button
                    type="submit"
                    icon="heroicon-m-paper-airplane"
                    label="Kirim pesan"
                    color="primary"
                    size="lg"
                    class="!rounded-pill"
                />
            </form>
        </div>

        <x-ui.icon-button
            icon="heroicon-m-chat-bubble-left-ellipsis"
            label="Buka chatbot"
            size="xl"
            class="mt-3 !rounded-pill !bg-etc-magenta !text-white shadow-panel transition hover:!bg-etc-primary"
            data-chatbot-toggle
            aria-expanded="false"
        />
    </section>
@endif
