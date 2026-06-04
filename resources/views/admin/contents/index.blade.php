<x-layouts.dashboard title="CMS Konten" area="admin" active="contents">
    <x-slot:headerActions>
        <a href="{{ route('admin.contents.create') }}" class="inline-flex min-h-11 items-center gap-2 rounded-pill bg-etc-magenta px-5 font-heading text-sm font-bold text-white shadow-soft">
            <span class="material-symbols-outlined text-base">add</span>
            Tambah Konten
        </a>
    </x-slot:headerActions>

    @php
        $mediaUrl = static function (?string $path): ?string {
            if (! $path) {
                return null;
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
            <div class="grid gap-3 lg:grid-cols-[1fr_190px_160px_auto]">
                <input name="search" value="{{ request('search') }}" placeholder="Cari judul atau slug" class="min-h-11 rounded-xl border border-etc-outline-variant px-4 text-sm focus:border-etc-magenta focus:outline-none">
                <select name="type" class="min-h-11 rounded-xl border border-etc-outline-variant px-4 text-sm focus:border-etc-magenta focus:outline-none">
                    <option value="">Semua tipe</option>
                    @foreach ($types as $type)
                        <option value="{{ $type }}" @selected(request('type') === $type)>{{ str($type)->replace('_', ' ')->headline() }}</option>
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

        <div class="grid gap-4 xl:grid-cols-2">
            @forelse ($contents as $content)
                <article class="rounded-card border border-etc-outline-variant bg-white p-5 shadow-panel">
                    <div class="flex gap-4">
                        <div class="h-24 w-24 shrink-0 overflow-hidden rounded-2xl bg-etc-surface-container">
                            @if ($content->image)
                                <img src="{{ $mediaUrl($content->image) }}" alt="{{ $content->title }}" class="h-full w-full object-cover">
                            @else
                                <div class="flex h-full w-full items-center justify-center text-etc-magenta">
                                    <span class="material-symbols-outlined text-4xl">dashboard_customize</span>
                                </div>
                            @endif
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="rounded-full bg-etc-surface-container px-3 py-1 font-heading text-[11px] font-black uppercase text-etc-magenta">{{ str($content->type)->replace('_', ' ')->headline() }}</span>
                                <span @class([
                                    'rounded-full px-3 py-1 font-heading text-[11px] font-black uppercase',
                                    'bg-green-50 text-green-700' => $content->is_published,
                                    'bg-amber-50 text-amber-700' => ! $content->is_published,
                                ])>{{ $content->is_published ? 'Published' : 'Draft' }}</span>
                            </div>
                            <h2 class="mt-3 truncate font-heading text-lg font-black">{{ $content->title }}</h2>
                            <p class="mt-1 text-sm text-etc-on-muted">{{ $content->slug ?: '-' }} · Urutan {{ (int) $content->display_order }}</p>
                            <p class="mt-3 line-clamp-2 text-sm leading-6 text-etc-on-muted">{{ $content->body ?: ($content->meta['value'] ?? 'Tidak ada isi.') }}</p>
                        </div>
                    </div>

                    <div class="mt-5 flex items-center justify-between gap-3">
                        <span class="text-xs font-bold uppercase text-etc-on-muted">{{ $content->updated_at?->format('d M Y H:i') }}</span>
                        <a href="{{ route('admin.contents.edit', $content) }}" class="inline-flex min-h-10 items-center gap-2 rounded-pill border border-etc-outline-variant px-4 font-heading text-sm font-bold text-etc-charcoal transition hover:border-etc-magenta hover:text-etc-magenta">
                            <span class="material-symbols-outlined text-base">edit</span>
                            Edit
                        </a>
                    </div>
                </article>
            @empty
                <div class="xl:col-span-2 rounded-card border border-dashed border-etc-outline-variant bg-white p-10 text-center shadow-panel">
                    <span class="material-symbols-outlined text-5xl text-etc-magenta">dashboard_customize</span>
                    <h2 class="mt-4 font-heading text-xl font-black">Konten belum tersedia</h2>
                    <a href="{{ route('admin.contents.create') }}" class="mt-5 inline-flex min-h-11 items-center rounded-pill bg-etc-magenta px-5 font-heading text-sm font-bold text-white">Tambah Konten</a>
                </div>
            @endforelse
        </div>

        <div>{{ $contents->links() }}</div>
    </section>
</x-layouts.dashboard>
