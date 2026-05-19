<x-layouts.public title="FAQ">
    <section class="bg-[#fff8f8] py-20">
        <div class="mx-auto max-w-[1120px] px-5 text-center lg:px-0">
            <p class="font-heading text-sm font-black uppercase tracking-[0.18em] text-etc-magenta">FAQ</p>
            <h1 class="mt-4 font-heading text-[42px] font-black leading-tight text-etc-on-surface md:text-[56px]">{{ $page?->title ?? 'FAQ ETC Planet' }}</h1>
            <p class="mx-auto mt-5 max-w-2xl text-[16px] leading-8 text-etc-on-muted">{{ $page?->body ?? 'Pertanyaan umum seputar program, biaya, jadwal, pendaftaran, pembayaran, dan placement test.' }}</p>
        </div>
    </section>

    <section class="bg-white py-20">
        <div class="mx-auto max-w-[880px] px-5 lg:px-0">
            <div class="space-y-4">
                @foreach ($faqs as $faq)
                    <details class="group rounded-[18px] border border-[#eeb8c9] bg-[#fff8f8] p-6 shadow-soft">
                        <summary class="flex cursor-pointer list-none items-center justify-between gap-4 font-heading text-lg font-black text-etc-on-surface">
                            {{ $faq['question'] }}
                            <span class="material-symbols-outlined text-etc-magenta group-open:rotate-45">add</span>
                        </summary>
                        <p class="mt-4 leading-7 text-etc-on-muted">{{ $faq['answer'] }}</p>
                    </details>
                @endforeach
            </div>

            <div class="mt-10 rounded-[22px] bg-etc-charcoal p-8 text-center text-white">
                <h2 class="font-heading text-2xl font-black">Masih punya pertanyaan?</h2>
                <p class="mt-3 text-white/75">Kirim pertanyaan melalui form kontak, atau coba chatbot public lewat endpoint yang sudah tersedia.</p>
                <a href="{{ route('public.contact.index') }}" class="mt-6 inline-flex min-h-12 items-center justify-center rounded-full bg-etc-magenta px-8 py-3 font-heading text-sm font-bold text-white">
                    Hubungi ETC Planet
                </a>
            </div>
        </div>
    </section>
</x-layouts.public>
