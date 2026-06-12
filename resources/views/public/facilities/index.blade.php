<x-layouts.public title="Fasilitas" :show-navbar="false" :show-footer="false" :show-chatbot="false">
    <x-public-discovery.navbar active="facilities" />
    @php
        $media = app(\App\Services\PublicDiscoveryService::class);
        $assetUrl = static fn (?string $path) => $media->mediaUrl($path, 'images/pu1-img.jpg');
    @endphp

    <section class="public-section bg-etc-surface">
        <div class="public-shell public-reveal" data-public-reveal>
            <p class="public-eyebrow">Fasilitas</p>
            <h1 class="public-title mt-4 max-w-3xl">Ruang belajar yang nyaman dan fokus</h1>
            <p class="public-subtitle mt-5 max-w-2xl">Setiap ruang dirancang agar siswa nyaman berdiskusi, latihan speaking, dan fokus mengikuti kelas.</p>
        </div>
    </section>

    <section class="public-section bg-etc-surface-low">
        <div class="public-shell">
            @if ($rooms->isNotEmpty())
                <div class="grid gap-5 md:grid-cols-3">
                    @foreach ($rooms as $room)
                        <article class="public-card overflow-hidden public-reveal" data-public-reveal>
                            <img src="{{ $assetUrl($room->image) }}" alt="{{ $room->title }}" class="h-56 w-full object-cover">
                            <div class="p-5">
                                <h2 class="font-heading text-xl font-bold">{{ $room->title }}</h2>
                                <p class="mt-3 min-h-20 text-sm leading-7 text-etc-on-muted">{{ $room->body }}</p>
                                <div class="mt-5 flex flex-wrap gap-2">
                                    @foreach (($room->meta['facility'] ?? []) as $facility)
                                        <x-ui.badge color="gray">{{ $facility }}</x-ui.badge>
                                    @endforeach
                                </div>
                                @if ($room->meta['capacity'] ?? null)
                                    <p class="mt-5 flex items-center gap-2 text-sm font-bold text-etc-magenta">
                                        <span class="material-symbols-outlined text-base">groups</span>
                                        Kapasitas {{ $room->meta['capacity'] }} siswa
                                    </p>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>
            @else
                <x-ui.empty-state
                    heading="Fasilitas belum tersedia"
                    description="Informasi ruang belajar ETC Planet akan tampil di sini setelah siap."
                    icon="heroicon-o-building-office-2"
                    contained
                />
            @endif
        </div>
    </section>
    <x-public-discovery.page-end />
</x-layouts.public>
