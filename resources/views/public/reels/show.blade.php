<x-layouts.public
    :title="$reel->title"
    :show-navbar="false"
    :show-footer="false"
    :show-chatbot="false"
    body-class="public-reels-page"
    main-class="h-screen"
>
    @php
        $media = app(\App\Services\PublicDiscoveryService::class);
        $assetUrl = static fn (?string $path, string $fallback = 'videos/video1.mp4'): string => $media->mediaUrl($path, $fallback);
    @endphp

    <section class="public-reel-slide min-h-screen" data-vertical-reel-detail>
        <x-ui.button
            :href="route('public.reels.index')"
            color="gray"
            size="sm"
            class="absolute left-4 top-4 z-20 !inline-flex !min-h-0 !items-center !gap-2 !rounded-none !bg-transparent !p-0 !font-heading !text-sm !font-bold !text-white/80 !shadow-none transition hover:!bg-transparent hover:!text-white md:left-6 md:top-6"
        >
            <span class="material-symbols-outlined text-base">arrow_back</span>
            Kembali
        </x-ui.button>

        <div class="public-reel-video-frame aspect-[9/16]">
            <video
                controls
                autoplay
                muted
                playsinline
                loop
                preload="metadata"
                poster="{{ $assetUrl($reel->thumbnail_path, 'images/pu1-img (3).jpg') }}"
                data-view-endpoint="{{ route('public.reels.views.store', $reel) }}"
            >
                <source src="{{ $assetUrl($reel->video_path) }}" type="video/mp4">
                Browser kamu tidak mendukung pemutar video.
            </video>

            <div class="public-reel-overlay">
                <p class="font-heading text-xs font-bold uppercase tracking-[0.16em] text-etc-magenta">{{ $reel->category ?? 'reel' }}</p>
                <h1 class="mt-2 max-w-[18rem] font-heading text-2xl font-bold leading-tight text-white">{{ $reel->title }}</h1>
                <p class="mt-2 max-w-[20rem] text-sm leading-6 text-white/75">{{ $reel->description ?: 'Cuplikan kegiatan ETC Planet.' }}</p>
            </div>
        </div>

        <div class="public-reel-actions" aria-label="Statistik reel">
            <span class="public-reel-action" aria-label="{{ number_format((int) $reel->views_count) }} views">
                <span class="material-symbols-outlined">visibility</span>
                <span data-views-count>{{ number_format((int) $reel->views_count) }}</span>
            </span>
            <x-ui.button
                type="button"
                color="gray"
                class="public-reel-action public-reel-like !min-h-0 !rounded-none !bg-transparent !p-0 !text-white !shadow-none hover:!bg-transparent"
                data-like-endpoint="{{ route('public.reels.likes.store', $reel) }}"
                data-liked="{{ $liked ? 'true' : 'false' }}"
                aria-label="Like {{ $reel->title }}"
                aria-pressed="{{ $liked ? 'true' : 'false' }}"
            >
                <span class="material-symbols-outlined" data-like-icon>favorite</span>
                <span data-likes-count>{{ number_format((int) $reel->likes_count) }}</span>
            </x-ui.button>
        </div>
    </section>
</x-layouts.public>
