<x-layouts.public
    title="Reels"
    :show-navbar="false"
    :show-footer="false"
    :show-chatbot="false"
    body-class="public-reels-page"
    main-class="h-screen"
>
    @php
        $media = app(\App\Services\PublicDiscoveryService::class);
        $assetUrl = static fn (?string $path, string $fallback = 'videos/video1.mp4'): string => $media->mediaUrl($path, $fallback);
        $likedReels = collect(session('liked_reels', []))->map(fn ($id) => (int) $id)->all();
    @endphp

    @if ($reels->isNotEmpty())
        <section class="public-reels-feed" data-vertical-reels-feed aria-label="ETC Planet Reels">
            @foreach ($reels as $reel)
                @php($liked = in_array((int) $reel->getKey(), $likedReels, true))

                <article class="public-reel-slide" data-reel-slide>
                    <x-ui.button
                        :href="route('public.home') . '#reels'"
                        color="gray"
                        size="sm"
                        class="absolute left-4 top-4 z-20 !inline-flex !min-h-0 !items-center !gap-2 !rounded-none !bg-transparent !p-0 !font-heading !text-sm !font-bold !text-white/80 !shadow-none transition hover:!bg-transparent hover:!text-white md:left-6 md:top-6"
                    >
                        <span class="material-symbols-outlined text-base">arrow_back</span>
                        Keluar
                    </x-ui.button>

                    <div class="public-reel-video-frame">
                        <video
                            muted
                            playsinline
                            loop
                            preload="metadata"
                            poster="{{ $assetUrl($reel->thumbnail_path, 'images/pu1-img (3).jpg') }}"
                            data-autoplay-reel="true"
                            data-view-endpoint="{{ route('public.reels.views.store', $reel) }}"
                            data-view-count-target="reel-views-{{ $reel->id }}"
                        >
                            <source src="{{ $assetUrl($reel->video_path) }}" type="video/mp4">
                            Browser kamu tidak mendukung pemutar video.
                        </video>

                        <a href="{{ route('public.reels.show', $reel) }}" class="public-reel-overlay">
                            <p class="font-heading text-xs font-bold uppercase tracking-[0.16em] text-etc-magenta">{{ $reel->category ?? 'reel' }}</p>
                            <h1 class="mt-2 max-w-[18rem] font-heading text-2xl font-bold leading-tight text-white">{{ $reel->title }}</h1>
                            <p class="mt-2 line-clamp-2 max-w-[20rem] text-sm leading-6 text-white/75">{{ $reel->description ?: 'Cuplikan kegiatan dan suasana belajar ETC Planet.' }}</p>
                        </a>
                    </div>

                    <div class="public-reel-actions" aria-label="Statistik reel">
                        <span class="public-reel-action" aria-label="{{ number_format((int) $reel->views_count) }} views">
                            <span class="material-symbols-outlined">visibility</span>
                            <span id="reel-views-{{ $reel->id }}">{{ number_format((int) $reel->views_count) }}</span>
                        </span>
                        <x-ui.button
                            type="button"
                            color="gray"
                            class="public-reel-action public-reel-like !min-h-0 !rounded-none !bg-transparent !p-0 !text-white !shadow-none hover:!bg-transparent"
                            data-like-endpoint="{{ route('public.reels.likes.store', $reel) }}"
                            data-liked="{{ $liked ? 'true' : 'false' }}"
                            data-likes-count-target="reel-likes-{{ $reel->id }}"
                            aria-label="Like {{ $reel->title }}"
                            aria-pressed="{{ $liked ? 'true' : 'false' }}"
                        >
                            <span class="material-symbols-outlined" data-like-icon>favorite</span>
                            <span id="reel-likes-{{ $reel->id }}">{{ number_format((int) $reel->likes_count) }}</span>
                        </x-ui.button>
                    </div>
                </article>
            @endforeach
        </section>
    @else
        <section class="grid min-h-screen place-items-center bg-etc-charcoal p-6 text-white">
            <x-ui.button
                :href="route('public.home') . '#reels'"
                color="gray"
                size="sm"
                class="absolute left-4 top-4 !inline-flex !min-h-0 !items-center !gap-2 !rounded-none !bg-transparent !p-0 !font-heading !text-sm !font-bold !text-white/80 !shadow-none transition hover:!bg-transparent hover:!text-white md:left-6 md:top-6"
            >
                <span class="material-symbols-outlined text-base">arrow_back</span>
                Keluar
            </x-ui.button>
            <div class="max-w-md text-center">
                <x-ui.empty-state
                    heading="Reels belum tersedia"
                    description="Cuplikan kelas dan kegiatan ETC Planet akan tampil di sini setelah dipublikasikan."
                    icon="heroicon-o-video-camera"
                    contained
                />
            </div>
        </section>
    @endif
</x-layouts.public>
