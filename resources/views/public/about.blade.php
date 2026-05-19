<x-layouts.public title="Tentang ETC Planet">
    @php
        $meta = $page?->meta ?? [];
        $missions = $meta['mission'] ?? [];
        $values = $meta['values'] ?? [];
    @endphp

    <section class="bg-[#fff8f8] py-20">
        <div class="mx-auto grid max-w-[1120px] gap-12 px-5 lg:grid-cols-[0.95fr_1.05fr] lg:px-0">
            <div>
                <p class="font-heading text-sm font-black uppercase tracking-[0.18em] text-etc-magenta">Tentang Kami</p>
                <h1 class="mt-4 font-heading text-[42px] font-black leading-tight text-etc-on-surface md:text-[56px]">{{ $page?->title ?? 'Tentang ETC Planet' }}</h1>
                <p class="mt-6 whitespace-pre-line text-[17px] leading-8 text-etc-on-muted">
                    {{ $page?->body ?? 'Profil ETC Planet belum dipublish. Silakan jalankan seeder public discovery atau isi CMS content type page dengan slug about.' }}
                </p>
            </div>
            <img src="{{ asset($page?->image ?: 'images/hero-img.jpeg') }}" alt="Suasana belajar ETC Planet" class="aspect-[4/3] w-full rounded-[24px] border-4 border-white object-cover shadow-panel">
        </div>
    </section>

    <section class="bg-[#fff0f3] py-20">
        <div class="mx-auto grid max-w-[1120px] gap-8 px-5 md:grid-cols-2 lg:px-0">
            <article class="rounded-[22px] border border-[#eeb8c9] bg-white p-8 shadow-soft">
                <span class="material-symbols-outlined text-4xl text-etc-magenta">visibility</span>
                <h2 class="mt-5 font-heading text-3xl font-black text-etc-on-surface">Visi</h2>
                <p class="mt-4 leading-7 text-etc-on-muted">{{ $meta['vision'] ?? 'Menjadi pusat pembelajaran bahasa yang ramah, modern, dan terukur.' }}</p>
            </article>
            <article class="rounded-[22px] border border-[#eeb8c9] bg-white p-8 shadow-soft">
                <span class="material-symbols-outlined text-4xl text-etc-magenta">flag</span>
                <h2 class="mt-5 font-heading text-3xl font-black text-etc-on-surface">Misi</h2>
                @if ($missions !== [])
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

    <section class="bg-white py-20">
        <div class="mx-auto max-w-[1120px] px-5 text-center lg:px-0">
            <h2 class="font-heading text-4xl font-black text-etc-on-surface">Nilai Belajar</h2>
            <div class="mt-10 flex flex-wrap justify-center gap-4">
                @forelse ($values as $value)
                    <span class="rounded-full bg-[#ffe6f3] px-6 py-3 font-heading text-sm font-black text-etc-on-surface">{{ $value }}</span>
                @empty
                    <span class="rounded-full bg-[#ffe6f3] px-6 py-3 font-heading text-sm font-black text-etc-on-surface">Friendly</span>
                    <span class="rounded-full bg-[#ffe6f3] px-6 py-3 font-heading text-sm font-black text-etc-on-surface">Focused</span>
                    <span class="rounded-full bg-[#ffe6f3] px-6 py-3 font-heading text-sm font-black text-etc-on-surface">Practical</span>
                @endforelse
            </div>
        </div>
    </section>
</x-layouts.public>
