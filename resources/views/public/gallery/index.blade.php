<x-layouts.public title="Galeri Kegiatan">
    @php($assetUrl = static fn (?string $path) => asset($path ?: 'images/pu1-img (3).jpg'))

    <section class="bg-[#fff8f8] py-20">
        <div class="mx-auto max-w-[1120px] px-5 text-center lg:px-0">
            <p class="font-heading text-sm font-black uppercase tracking-[0.18em] text-etc-magenta">Galeri</p>
            <h1 class="mt-4 font-heading text-[42px] font-black leading-tight text-[#2a1820] md:text-[56px]">Kegiatan belajar ETC Planet</h1>
            <p class="mx-auto mt-5 max-w-2xl text-[16px] leading-8 text-[#765f67]">Galeri berasal dari CMS contents type gallery.</p>
        </div>
    </section>

    <section class="bg-[#2b2b2b] py-20 text-white">
        <div class="mx-auto max-w-[1120px] px-5 lg:px-0">
            @if ($galleryItems->isNotEmpty())
                <div class="grid gap-7 md:grid-cols-3">
                    @foreach ($galleryItems as $item)
                        <article class="overflow-hidden rounded-[22px] border border-white/10 bg-white/5 shadow-[0_18px_34px_rgba(0,0,0,0.28)]">
                            <img src="{{ $assetUrl($item->image) }}" alt="{{ $item->title }}" class="h-64 w-full object-cover opacity-95">
                            <div class="p-6">
                                <p class="font-heading text-xs font-black uppercase tracking-[0.16em] text-etc-magenta">{{ $item->meta['event_date'] ?? 'ETC Planet' }}</p>
                                <h2 class="mt-3 font-heading text-2xl font-black">{{ $item->title }}</h2>
                                <p class="mt-3 text-sm leading-7 text-zinc-300">{{ $item->body }}</p>
                                <p class="mt-5 flex items-center gap-2 text-sm text-zinc-400">
                                    <span class="material-symbols-outlined text-base">location_on</span>
                                    {{ $item->meta['location'] ?? 'Padang' }}
                                </p>
                            </div>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="rounded-[22px] border border-white/10 bg-white/5 p-10 text-center text-zinc-300">
                    Galeri kegiatan belum dipublish.
                </div>
            @endif
        </div>
    </section>
</x-layouts.public>
