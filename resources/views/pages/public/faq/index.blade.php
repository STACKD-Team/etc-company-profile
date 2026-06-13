<x-layouts.public title="FAQ" :show-navbar="false" :show-footer="false" :show-chatbot="false">
    <x-public-discovery.navbar active="faq" />
    <section class="public-faq-hero bg-etc-surface">
        <div class="public-shell-narrow text-center public-reveal" data-public-reveal>
            <p class="public-eyebrow">FAQ</p>
            <h1 class="public-title mt-4">FAQ ETC Planet</h1>
            <p class="public-subtitle mx-auto mt-5 max-w-2xl">Pertanyaan umum seputar program, biaya, jadwal, pendaftaran, pembayaran, dan placement test.</p>
        </div>
    </section>

    <section class="public-faq-content bg-etc-surface-low">
        <div class="public-shell-narrow">
            @if ($faqs !== [])
                <div class="public-faq-list" data-public-faq>
                    @foreach ($faqs as $index => $faq)
                        <article class="public-faq-item public-reveal" data-public-reveal data-faq-item>
                            <x-ui.button
                                type="button"
                                color="gray"
                                class="public-faq-question"
                                data-faq-toggle
                                aria-expanded="false"
                                aria-controls="faq-answer-{{ $index }}"
                            >
                                <span>{{ $faq['question'] }}</span>
                                <span class="material-symbols-outlined public-faq-arrow" data-faq-arrow>expand_more</span>
                            </x-ui.button>

                            <div
                                id="faq-answer-{{ $index }}"
                                class="public-faq-answer hidden"
                                data-faq-answer
                            >
                                <p>{{ $faq['answer'] }}</p>
                            </div>
                        </article>
                    @endforeach
                </div>
            @else
                <x-ui.empty-state
                    heading="FAQ belum tersedia"
                    description="Pertanyaan dan jawaban resmi ETC Planet akan tampil setelah dipublikasikan."
                    icon="heroicon-o-question-mark-circle"
                    contained
                />
            @endif

            <div class="public-card mt-8 bg-etc-charcoal p-6 text-center text-white public-reveal" data-public-reveal>
                <h2 class="font-heading text-2xl font-bold">Masih punya pertanyaan?</h2>
                <p class="mx-auto mt-3 max-w-xl text-white/75">Kirim pertanyaan melalui form kontak, atau buka chatbot untuk bantuan cepat seputar program dan pendaftaran.</p>
                <div class="mt-6">
                    <x-ui.button :href="route('public.contact.index')" size="xl" icon="heroicon-m-arrow-right" icon-position="after">
                        Hubungi ETC Planet
                    </x-ui.button>
                </div>
            </div>
        </div>
    </section>
    <x-public-discovery.page-end />
</x-layouts.public>
