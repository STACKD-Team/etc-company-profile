<x-layouts.dashboard title="Admin Reels" area="admin" active="reels">
    <x-slot:headerActions>
        <a href="{{ route('admin.reels.create') }}" class="inline-flex min-h-11 items-center gap-2 rounded-pill bg-etc-magenta px-5 font-heading text-sm font-bold text-white shadow-soft">
            <span class="material-symbols-outlined text-base">add</span>
            Tambah Reel
        </a>
    </x-slot:headerActions>

    @php
        $mediaUrl = static function (?string $path, ?string $fallback = null): ?string {
            if (! $path) {
                return $fallback ? asset($fallback) : null;
            }

            return \Illuminate\Support\Str::startsWith($path, ['http://', 'https://', '/', 'images/', 'videos/', 'storage/'])
                ? asset(ltrim($path, '/'))
                : \Illuminate\Support\Facades\Storage::url($path);
        };
    @endphp

    <section class="space-y-5">
        @if (session('status'))
            <div class="rounded-card border border-green-200 bg-green-50 px-5 py-4 font-heading text-sm font-bold text-green-700">{{ session('status') }}</div>
        @endif

        <form method="GET" class="rounded-card bg-white p-5 shadow-panel">
            <div class="grid gap-3 lg:grid-cols-[1fr_180px_160px_auto]">
                <input name="search" value="{{ request('search') }}" placeholder="Cari judul reel" class="min-h-11 rounded-xl border border-etc-outline-variant px-4 text-sm focus:border-etc-magenta focus:outline-none">
                <select name="category" class="min-h-11 rounded-xl border border-etc-outline-variant px-4 text-sm focus:border-etc-magenta focus:outline-none">
                    <option value="">Semua kategori</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category }}" @selected(request('category') === $category)>{{ ucfirst($category) }}</option>
                    @endforeach
                </select>
                <select name="is_published" class="min-h-11 rounded-xl border border-etc-outline-variant px-4 text-sm focus:border-etc-magenta focus:outline-none">
                    <option value="">Semua status</option>
                    <option value="1" @selected(request('is_published') === '1')>Published</option>
                    <option value="0" @selected(request('is_published') === '0')>Draft</option>
                </select>
                <button class="inline-flex min-h-11 items-center justify-center gap-2 rounded-pill bg-etc-charcoal px-5 font-heading text-sm font-bold text-white">
                    <span class="material-symbols-outlined text-base">search</span>
                    Filter
                </button>
            </div>
        </form>

        <div class="grid gap-5 xl:grid-cols-3">
            @forelse ($reels as $reel)
                <article class="overflow-hidden rounded-card border border-etc-outline-variant bg-white shadow-panel">
                    <div class="grid sm:grid-cols-[150px_1fr]">
                        <a href="{{ route('admin.reels.edit', $reel) }}" class="relative block aspect-[9/14] bg-etc-charcoal sm:aspect-auto">
                            @if ($reel->thumbnail_path || $reel->video_path)
                                <video muted playsinline preload="metadata" poster="{{ $mediaUrl($reel->thumbnail_path, 'images/pu1-img (3).jpg') }}" class="h-full w-full object-cover">
                                    <source src="{{ $mediaUrl($reel->video_path, 'videos/video1.mp4') }}" type="video/mp4">
                                </video>
                            @else
                                <div class="flex h-full items-center justify-center text-white/60">
                                    <span class="material-symbols-outlined text-5xl">smart_display</span>
                                </div>
                            @endif
                            <span class="absolute left-3 top-3 rounded-full bg-etc-magenta px-3 py-1 font-heading text-[11px] font-black uppercase text-white">{{ $reel->category ?: 'edukasi' }}</span>
                        </a>
                        <div class="flex min-w-0 flex-col p-5">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <h2 class="truncate font-heading text-lg font-black text-etc-on-surface">{{ $reel->title }}</h2>
                                    <p class="mt-2 line-clamp-2 text-sm leading-6 text-etc-on-muted">{{ $reel->description ?: 'Tidak ada deskripsi.' }}</p>
                                </div>
                                <span @class([
                                    'rounded-full px-3 py-1 font-heading text-[11px] font-black uppercase',
                                    'bg-green-50 text-green-700' => $reel->is_published,
                                    'bg-amber-50 text-amber-700' => ! $reel->is_published,
                                ])>{{ $reel->is_published ? 'Published' : 'Draft' }}</span>
                            </div>

                            <div class="mt-5 grid grid-cols-3 gap-2 text-sm">
                                <span class="rounded-xl bg-etc-surface-container px-3 py-2 text-center font-bold text-etc-on-muted">{{ number_format((int) $reel->views_count) }} views</span>
                                <span class="rounded-xl bg-etc-surface-container px-3 py-2 text-center font-bold text-etc-magenta">{{ number_format((int) $reel->likes_count) }} likes</span>
                                <span class="rounded-xl bg-etc-surface-container px-3 py-2 text-center font-bold text-etc-on-muted">{{ $reel->duration_seconds ? $reel->duration_seconds.'s' : '-' }}</span>
                            </div>

                            <a href="{{ route('admin.reels.edit', $reel) }}" class="mt-5 inline-flex min-h-10 items-center justify-center gap-2 rounded-pill border border-etc-outline-variant px-4 font-heading text-sm font-bold text-etc-charcoal transition hover:border-etc-magenta hover:text-etc-magenta">
                                <span class="material-symbols-outlined text-base">edit</span>
                                Edit
                            </a>
                        </div>
                    </div>
                </article>
            @empty
                <div class="xl:col-span-3 rounded-card border border-dashed border-etc-outline-variant bg-white p-10 text-center shadow-panel">
                    <span class="material-symbols-outlined text-5xl text-etc-magenta">video_library</span>
                    <h2 class="mt-4 font-heading text-xl font-black">Belum ada reels</h2>
                    <a href="{{ route('admin.reels.create') }}" class="mt-5 inline-flex min-h-11 items-center rounded-pill bg-etc-magenta px-5 font-heading text-sm font-bold text-white">Tambah Reel</a>
                </div>
            @endforelse
        </div>

        <div>{{ $reels->links() }}</div>
    </section>
</x-layouts.dashboard>
