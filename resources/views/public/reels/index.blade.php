<x-layouts.public title="Reels">
    @php($assetUrl = static fn (?string $path, string $fallback = 'videos/video1.mp4') => asset($path ?: $fallback))

    <section class="bg-[#2b2b2b] py-20 text-white">
        <div class="mx-auto max-w-[1120px] px-5 lg:px-0">
            <p class="font-heading text-sm font-black uppercase tracking-[0.18em] text-etc-magenta">ETC Planet Reels</p>
            <h1 class="mt-4 max-w-3xl font-heading text-[42px] font-black leading-tight md:text-[56px]">Cuplikan suasana belajar dan kegiatan ETC</h1>
            <p class="mt-5 max-w-2xl text-[16px] leading-8 text-zinc-400">Hanya reels published yang ditampilkan di halaman public.</p>
        </div>
    </section>

    <section class="bg-[#fff8f8] py-20">
        <div class="mx-auto max-w-[1120px] px-5 lg:px-0">
            @if ($reels->isNotEmpty())
                <div class="grid gap-8 md:grid-cols-3 lg:grid-cols-4">
                    @foreach ($reels as $reel)
                        <a href="{{ route('public.reels.show', $reel) }}" class="group overflow-hidden rounded-[22px] border border-[#eeb8c9] bg-white shadow-soft">
                            <div class="relative aspect-[9/14] overflow-hidden bg-black">
                                <video preload="metadata" muted playsinline poster="{{ asset($reel->thumbnail_path ?: 'images/pu1-img (3).jpg') }}" class="h-full w-full object-cover opacity-90">
                                    <source src="{{ $assetUrl($reel->video_path) }}" type="video/mp4">
                                </video>
                                <div class="absolute inset-0 bg-gradient-to-t from-black/75 to-transparent"></div>
                                <span class="absolute left-4 top-4 rounded-full bg-etc-magenta px-3 py-1 font-heading text-xs font-black uppercase text-white">{{ $reel->category }}</span>
                                <span class="absolute bottom-4 left-4 right-4 font-heading text-base font-black text-white">{{ $reel->title }}</span>
                            </div>
                            <div class="flex items-center justify-between p-5 text-sm text-[#765f67]">
                                <span class="flex items-center gap-1"><span class="material-symbols-outlined text-base">visibility</span>{{ number_format((int) $reel->views_count) }}</span>
                                <span class="flex items-center gap-1 text-etc-magenta"><span class="material-symbols-outlined text-base">favorite</span>{{ number_format((int) $reel->likes_count) }}</span>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="rounded-[22px] border border-dashed border-[#eeb8c9] bg-white p-10 text-center shadow-soft">
                    <span class="material-symbols-outlined text-5xl text-etc-magenta">smart_display</span>
                    <h2 class="mt-4 font-heading text-2xl font-black text-[#2a1820]">Reels belum dipublish</h2>
                    <p class="mt-3 text-[#765f67]">Upload dan publish reels dari modul admin CMS pada sprint berikutnya.</p>
                </div>
            @endif
        </div>
    </section>
</x-layouts.public>
