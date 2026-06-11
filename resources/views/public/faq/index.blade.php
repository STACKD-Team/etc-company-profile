<x-layouts.public title="FAQ">
    <section class="public-section bg-etc-surface">
        <div class="public-shell-narrow text-center public-reveal" data-public-reveal>
            <p class="public-eyebrow">FAQ</p>
            <h1 class="public-title mt-4">{{ $page?->title ?? 'FAQ ETC Planet' }}</h1>
            <p class="public-subtitle mx-auto mt-5 max-w-2xl">{{ $page?->body ?? 'Pertanyaan umum seputar program, biaya, jadwal, pendaftaran, pembayaran, dan placement test.' }}</p>
        </div>
    </section>

    <section class="public-section bg-etc-surface-low">
        <div class="public-shell-narrow">
            <div class="space-y-3">
                @foreach ($faqs as $faq)
                    <article class="public-card p-5 public-reveal" data-public-reveal x-data="{ open: false }">
                        <x-ui.button
                            type="button"
                            color="gray"
                            class="!flex !w-full !min-h-0 !items-center !justify-between !gap-4 !rounded-none !bg-transparent !p-0 !text-left !font-heading !text-lg !font-bold !text-etc-on-surface !shadow-none hover:!bg-transparent"
                            x-on:click="open = ! open"
                            x-bind:aria-expanded="open.toString()"
                        >
                            {{ $faq['question'] }}
                            <span class="material-symbols-outlined text-etc-magenta transition" x-bind:class="open ? 'rotate-45' : ''">add</span>
                        </x-ui.button>
                        <p class="mt-4 leading-7 text-etc-on-muted" x-show="open">{{ $faq['answer'] }}</p>
                    </article>
                @endforeach
            </div>

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
</x-layouts.public>
