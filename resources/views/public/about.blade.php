<x-layouts.public title="Tentang ETC Planet" navbar-active="about">
    @php
        $meta = $page?->meta ?? [];
        $missions = collect($meta['mission'] ?? [])->filter();
        $values = collect($meta['values'] ?? ['Friendly', 'Focused', 'Practical'])->filter();
        $media = app(\App\Services\PublicDiscoveryService::class);
    @endphp

    <section class="public-section bg-etc-surface">
        <div class="public-shell grid items-center gap-10 lg:grid-cols-[0.95fr_1.05fr]">
            <div class="public-reveal" data-public-reveal>
                <p class="public-eyebrow">Tentang Kami</p>
                <h1 class="public-title mt-4">{{ $page?->title ?? 'Tentang ETC Planet' }}</h1>
                <p class="public-subtitle mt-6 whitespace-pre-line">
                    {{ $page?->body ?? 'ETC Planet membantu siswa belajar bahasa dengan kelas kecil, pengajar yang suportif, dan latihan yang dekat dengan kebutuhan sehari-hari.' }}
                </p>
            </div>
            <div class="relative public-reveal" data-public-reveal>
                <div class="absolute -right-4 -top-4 h-28 w-28 rounded-[42%_58%_49%_51%] bg-etc-surface-high"></div>
                <img src="{{ $media->mediaUrl($page?->image, 'images/hero-img.jpeg') }}" alt="Suasana belajar ETC Planet" class="relative aspect-[4/3] w-full rounded-card border-2 border-etc-outline-variant object-cover shadow-panel">
            </div>
        </div>
    </section>

    <section class="public-section bg-etc-surface-low">
        <div class="public-shell grid gap-5 md:grid-cols-2">
            <article class="public-card p-6 public-reveal" data-public-reveal>
                <span class="flex h-12 w-12 items-center justify-center rounded-selector bg-etc-surface-container text-etc-magenta">
                    <span class="material-symbols-outlined">visibility</span>
                </span>
                <h2 class="mt-5 font-heading text-2xl font-bold">Visi</h2>
                <p class="mt-4 leading-7 text-etc-on-muted">{{ $meta['vision'] ?? 'Menjadi pusat pembelajaran bahasa yang ramah, modern, dan terukur.' }}</p>
            </article>

            <article class="public-card p-6 public-reveal" data-public-reveal>
                <span class="flex h-12 w-12 items-center justify-center rounded-selector bg-etc-surface-container text-etc-magenta">
                    <span class="material-symbols-outlined">flag</span>
                </span>
                <h2 class="mt-5 font-heading text-2xl font-bold">Misi</h2>
                @if ($missions->isNotEmpty())
                    <ul class="mt-4 space-y-3 text-etc-on-muted">
                        @foreach ($missions as $mission)
                            <li class="flex gap-3">
                                <span class="material-symbols-outlined mt-0.5 text-base text-etc-magenta">check_circle</span>
                                <span>{{ $mission }}</span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="mt-4 leading-7 text-etc-on-muted">Misi ETC Planet akan tampil setelah konten about dipublish.</p>
                @endif
            </article>
        </div>
    </section>

    <section class="public-section bg-etc-surface">
        <div class="public-shell-narrow text-center public-reveal" data-public-reveal>
            <p class="public-eyebrow">Nilai Belajar</p>
            <h2 class="mt-3 font-heading text-4xl font-bold">Budaya kelas yang hangat dan terarah</h2>
            <div class="mt-8 flex flex-wrap justify-center gap-3">
                @foreach ($values as $value)
                    <x-ui.badge color="gray">{{ $value }}</x-ui.badge>
                @endforeach
            </div>
        </div>
    </section>
</x-layouts.public>
