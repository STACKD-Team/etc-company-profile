<x-layouts.public :title="$reel->title">
    @php($assetUrl = static fn (?string $path, string $fallback = 'videos/video1.mp4') => asset($path ?: $fallback))

    <section class="bg-[#111] py-12 text-white md:py-20">
        <div class="mx-auto grid max-w-[1120px] gap-10 px-5 lg:grid-cols-[430px_1fr] lg:px-0">
            <div class="overflow-hidden rounded-[28px] border border-white/15 bg-black shadow-[0_24px_60px_rgba(0,0,0,0.45)]">
                <video controls autoplay muted playsinline poster="{{ asset($reel->thumbnail_path ?: 'images/pu1-img (3).jpg') }}" class="aspect-[9/14] w-full object-cover" data-view-endpoint="{{ route('public.reels.views.store', $reel) }}">
                    <source src="{{ $assetUrl($reel->video_path) }}" type="video/mp4">
                    Browser kamu tidak mendukung pemutar video.
                </video>
            </div>

            <div class="flex flex-col justify-center">
                <a href="{{ route('public.reels.index') }}" class="mb-8 inline-flex items-center gap-2 text-sm font-bold text-zinc-400 hover:text-white">
                    <span class="material-symbols-outlined text-base">arrow_back</span>
                    Kembali ke reels
                </a>
                <p class="font-heading text-sm font-black uppercase tracking-[0.18em] text-etc-magenta">{{ $reel->category }}</p>
                <h1 class="mt-4 font-heading text-[38px] font-black leading-tight md:text-[54px]">{{ $reel->title }}</h1>
                <p class="mt-6 text-[16px] leading-8 text-zinc-300">{{ $reel->description ?: 'Cuplikan kegiatan ETC Planet.' }}</p>

                <div class="mt-8 flex flex-wrap gap-4">
                    <span class="inline-flex min-h-11 items-center gap-2 rounded-full bg-white/10 px-5 py-2 font-heading text-sm font-bold text-white">
                        <span class="material-symbols-outlined text-base">visibility</span>
                        <span data-views-count>{{ number_format((int) $reel->views_count) }}</span> views
                    </span>
                    <button type="button" data-like-endpoint="{{ route('public.reels.likes.store', $reel) }}" data-liked="{{ $liked ? 'true' : 'false' }}" class="inline-flex min-h-11 items-center gap-2 rounded-full bg-etc-magenta px-5 py-2 font-heading text-sm font-bold text-white">
                        <span class="material-symbols-outlined text-base">favorite</span>
                        <span data-likes-count>{{ number_format((int) $reel->likes_count) }}</span>
                    </button>
                </div>
            </div>
        </div>
    </section>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                const video = document.querySelector('[data-view-endpoint]');
                const likeButton = document.querySelector('[data-like-endpoint]');

                if (video && token) {
                    fetch(video.dataset.viewEndpoint, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json'
                        }
                    })
                        .then((response) => response.ok ? response.json() : null)
                        .then((data) => {
                            if (data?.views_count !== undefined) {
                                document.querySelector('[data-views-count]').textContent = new Intl.NumberFormat().format(data.views_count);
                            }
                        })
                        .catch(() => {});
                }

                if (likeButton && token) {
                    likeButton.addEventListener('click', () => {
                        fetch(likeButton.dataset.likeEndpoint, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': token,
                                'Accept': 'application/json'
                            }
                        })
                            .then((response) => response.ok ? response.json() : null)
                            .then((data) => {
                                if (!data) {
                                    return;
                                }

                                likeButton.dataset.liked = data.liked ? 'true' : 'false';
                                likeButton.classList.toggle('bg-etc-magenta', data.liked);
                                likeButton.classList.toggle('bg-white/10', !data.liked);
                                document.querySelector('[data-likes-count]').textContent = new Intl.NumberFormat().format(data.likes_count);
                            })
                            .catch(() => {});
                    });
                }
            });
        </script>
    @endpush
</x-layouts.public>
