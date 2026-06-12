@if (\Illuminate\Support\Facades\Route::has('public.chatbot.messages.store'))
    <section
        class="public-discovery-chatbot"
        data-chatbot-widget
        data-chatbot-endpoint="{{ route('public.chatbot.messages.store') }}"
        aria-label="Chatbot ETC Planet"
    >
        <div class="public-discovery-chatbot__panel hidden" data-chatbot-panel>
            <div class="public-discovery-chatbot__header">
                <div class="flex items-center gap-3">
                    <span class="public-discovery-chatbot__avatar">
                        <span class="material-symbols-outlined">smart_toy</span>
                    </span>
                    <div>
                        <p class="font-heading text-sm font-bold">ETC Planet Bot</p>
                        <p class="mt-0.5 text-xs text-white/75">Siap membantu</p>
                    </div>
                </div>
                <x-ui.icon-button
                    icon="heroicon-m-x-mark"
                    label="Tutup chatbot"
                    color="gray"
                    size="sm"
                    class="!rounded-pill !text-white/85 hover:!bg-white/10 hover:!text-white"
                    data-chatbot-close
                />
            </div>

            <div class="public-discovery-chatbot__messages" data-chatbot-messages>
                <div class="public-discovery-chatbot__bot-row">
                    <span class="public-discovery-chatbot__mini-avatar">
                        <span class="material-symbols-outlined">smart_toy</span>
                    </span>
                    <p class="public-discovery-chatbot__bubble public-discovery-chatbot__bubble--bot">
                        Halo! Ada yang bisa saya bantu tentang program kursus di ETC Planet?
                    </p>
                </div>

                <div class="public-discovery-chatbot__suggestions" data-chatbot-suggestions>
                    @foreach (['Program yang tersedia', 'Biaya pendaftaran', 'Jadwal belajar'] as $suggestion)
                        <x-ui.button
                            type="button"
                            color="gray"
                            size="sm"
                            class="public-discovery-chatbot__suggestion"
                            data-chatbot-suggestion="{{ $suggestion }}"
                        >
                            {{ $suggestion }}
                        </x-ui.button>
                    @endforeach
                </div>
            </div>

            <form class="public-discovery-chatbot__form" data-chatbot-form>
                <x-ui.field
                    id="public-discovery-chatbot-message"
                    name="message"
                    label="Pesan chatbot"
                    label-class="sr-only"
                    wrapper-class="min-w-0 flex-1"
                    maxlength="1000"
                    required
                    autocomplete="off"
                    size="lg"
                    placeholder="Ketik pesan..."
                />
                <x-ui.icon-button
                    type="submit"
                    icon="heroicon-m-paper-airplane"
                    label="Kirim pesan"
                    color="primary"
                    size="lg"
                    class="public-discovery-chatbot__send !rounded-pill"
                    data-chatbot-submit
                />
            </form>
        </div>

        <x-ui.button
            type="button"
            color="primary"
            size="xl"
            class="public-discovery-chatbot__toggle"
            data-chatbot-toggle
            aria-expanded="false"
            aria-label="Buka chatbot"
        >
            <span class="material-symbols-outlined">smart_toy</span>
        </x-ui.button>
    </section>
@endif
