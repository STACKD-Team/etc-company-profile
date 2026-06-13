<x-layouts.dashboard :title="$reel->title" area="admin" active="reels">
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

    @if (session('status'))
        <x-ui.alert status="success" class="mb-5">{{ session('status') }}</x-ui.alert>
    @endif

    <x-ui.resource-header
        :title="$reel->title"
        :subtitle="$reel->description ?: 'Detail reel dan status publikasi.'"
        :back-url="route('admin.reel.index')"
    >
        <x-slot:status>
            <x-ui.badge :status="$reel->is_published ? 'published' : 'draft'">{{ $reel->is_published ? 'Published' : 'Draft' }}</x-ui.badge>
        </x-slot:status>
        <x-slot:actions>
            <x-ui.button :href="route('admin.reel.edit', $reel)" icon="heroicon-m-pencil-square">Edit</x-ui.button>
        </x-slot:actions>
    </x-ui.resource-header>

    <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_360px]">
        <x-ui.detail-card heading="Detail Reel">
            <x-ui.description-list>
                <x-ui.description-item label="Kategori" :value="ucfirst($reel->category ?: 'edukasi')" />
                <x-ui.description-item label="Durasi" :value="$reel->duration_seconds ? $reel->duration_seconds.' detik' : '-'" />
                <x-ui.description-item label="Views" :value="number_format((int) $reel->views_count)" />
                <x-ui.description-item label="Likes" :value="number_format((int) $reel->likes_count)" />
                <x-ui.description-item label="Published At" :value="$reel->published_at?->format('d M Y H:i') ?: '-'" />
                <x-ui.description-item label="Status">
                    <x-ui.badge :status="$reel->is_published ? 'published' : 'draft'">{{ $reel->is_published ? 'Published' : 'Draft' }}</x-ui.badge>
                </x-ui.description-item>
            </x-ui.description-list>

            <div class="mt-5">
                <h2 class="font-heading text-sm font-bold text-etc-on-surface">Deskripsi</h2>
                <p class="mt-2 text-sm leading-6 text-etc-on-muted">{{ $reel->description ?: '-' }}</p>
            </div>
        </x-ui.detail-card>

        <x-ui.detail-card heading="Preview Media">
            <div class="overflow-hidden rounded-box border-2 border-etc-outline-variant/60 bg-etc-charcoal">
                @if ($reel->video_path)
                    <video controls playsinline preload="metadata" poster="{{ $mediaUrl($reel->thumbnail_path) }}" class="aspect-[9/16] w-full bg-black object-cover">
                        <source src="{{ $mediaUrl($reel->video_path) }}" type="video/mp4">
                    </video>
                @elseif ($reel->thumbnail_path)
                    <img src="{{ $mediaUrl($reel->thumbnail_path) }}" alt="{{ $reel->title }}" class="aspect-[9/16] w-full object-cover">
                @else
                    <div class="flex aspect-[9/16] items-center justify-center text-white/70">
                        <span class="material-symbols-outlined text-6xl">smart_display</span>
                    </div>
                @endif
            </div>
        </x-ui.detail-card>
    </div>
</x-layouts.dashboard>
