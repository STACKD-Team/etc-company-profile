<x-layouts.public title="Galeri Kegiatan">
    @php
        $media = app(\App\Services\PublicDiscoveryService::class);
        $assetUrl = static fn (?string $path, string $fallback = 'images/pu1-img (3).jpg'): string => $media->mediaUrl($path, $fallback);
    @endphp

    <section class="public-section bg-etc-surface">
        <div class="public-shell-narrow text-center public-reveal" data-public-reveal>
            <p class="public-eyebrow">Galeri</p>
            <h1 class="public-title mt-4">Kegiatan belajar ETC Planet</h1>
            <p class="public-subtitle mx-auto mt-5 max-w-2xl">Lihat suasana kelas, aktivitas siswa, dan momen belajar yang membuat bahasa terasa lebih dekat.</p>
        </div>
    </section>

    <section class="public-section bg-etc-charcoal text-white">
        <div class="public-shell">
            @if ($galleryItems->isNotEmpty())
                <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                    @foreach ($galleryItems as $item)
                        @php
                            $images = collect($item->images ?: [])
                                ->prepend($item->image)
                                ->filter()
                                ->unique()
                                ->values();
                        @endphp

                        <article class="public-card-dark overflow-hidden public-reveal" data-public-reveal data-gallery-card>
                            <div class="relative bg-black">
                                <img src="{{ $assetUrl($images->first()) }}" alt="{{ $item->meta['alt'] ?? $item->title }}" class="h-64 w-full object-cover opacity-95">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent"></div>
                                <x-ui.badge color="primary" class="absolute bottom-4 left-4 !bg-etc-surface !text-etc-magenta">
                                    {{ $item->meta['category'] ?? 'Kegiatan' }}
                                </x-ui.badge>
                            </div>

                            @if ($images->count() > 1)
                                <div class="grid grid-cols-3 gap-2 border-b-2 border-etc-surface/10 bg-etc-surface/5 p-3">
                                    @foreach ($images->skip(1)->take(3) as $image)
                                        <img src="{{ $assetUrl($image) }}" alt="Foto tambahan {{ $item->title }}" class="h-20 w-full rounded-card object-cover">
                                    @endforeach
                                </div>
                            @endif

                            <div class="p-5">
                                <p class="font-heading text-xs font-bold uppercase tracking-[0.16em] text-etc-magenta">{{ $item->meta['event_date'] ?? 'ETC Planet' }}</p>
                                <h2 class="mt-3 font-heading text-xl font-bold text-white">{{ $item->title }}</h2>
                                <p class="mt-3 text-sm leading-7 text-white/75">{{ $item->body ?: 'Dokumentasi kegiatan belajar ETC Planet.' }}</p>
                                <p class="mt-5 flex items-center gap-2 text-sm text-white/60">
                                    <span class="material-symbols-outlined text-base">location_on</span>
                                    {{ $item->meta['location'] ?? 'ETC Planet Padang' }}
                                </p>
                            </div>
                        </article>
                    @endforeach
                </div>
            @else
                <x-ui.empty-state
                    heading="Galeri kegiatan belum tersedia"
                    description="Foto kelas dan dokumentasi kegiatan akan tampil di sini setelah dipublikasikan."
                    icon="heroicon-o-photo"
                    contained
                />
            @endif
        </div>
    </section>
</x-layouts.public>
