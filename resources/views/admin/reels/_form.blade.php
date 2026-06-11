@csrf
@if ($reel->exists) @method('PUT') @endif

@php
    $mediaUrl = static function (?string $path): ?string {
        if (! $path) {
            return null;
        }

        return \Illuminate\Support\Str::startsWith($path, ['http://', 'https://', '/', 'images/', 'videos/', 'storage/'])
            ? asset(ltrim($path, '/'))
            : \Illuminate\Support\Facades\Storage::url($path);
    };
    $categoryOptions = collect($categories)->mapWithKeys(fn ($category) => [$category => ucfirst($category)])->all();
@endphp

<x-ui.panel heading="Detail Reel" description="Kelola metadata dan media reels tanpa mengubah storage workflow Sprint 1." class="min-w-0">
    <div class="grid gap-5 md:grid-cols-2">
        <div class="md:col-span-2">
            <x-ui.field name="title" label="Judul" :value="$reel->title" required />
        </div>

        <x-ui.select name="category" label="Kategori" :value="$reel->category ?: 'edukasi'" :options="$categoryOptions" required />
        <x-ui.number-field name="duration_seconds" label="Durasi Detik" :value="$reel->duration_seconds" min="1" />

        <div class="md:col-span-2">
            <x-ui.textarea name="description" label="Deskripsi" rows="5" :value="$reel->description" />
        </div>

        <x-ui.file-upload name="video" label="Video" accept="video/mp4,video/quicktime,video/webm" :required="! $reel->exists" />
        <x-ui.file-upload name="thumbnail" label="Thumbnail" accept="image/*" />

        <div class="md:col-span-2">
            <x-ui.checkbox name="is_published" label="Published" helper="Reel published dapat tampil di public reels." :checked="(bool) $reel->is_published" />
        </div>
    </div>
</x-ui.panel>

<x-ui.panel heading="Preview" description="Video aktif akan tampil di sini setelah upload tersimpan." class="min-w-0">
    <div class="overflow-hidden rounded-box border-2 border-etc-outline-variant/60 bg-etc-charcoal">
        @if ($reel->video_path)
            <video controls muted playsinline poster="{{ $mediaUrl($reel->thumbnail_path) }}" class="aspect-[9/14] w-full object-cover">
                <source src="{{ $mediaUrl($reel->video_path) }}" type="video/mp4">
            </video>
        @else
            <div class="flex aspect-[9/14] items-center justify-center text-etc-surface/70">
                <span class="material-symbols-outlined text-6xl">smart_display</span>
            </div>
        @endif
    </div>
    <p class="mt-4 break-all text-sm text-etc-on-muted">{{ $reel->video_path ?: 'Video baru akan tampil setelah disimpan.' }}</p>
</x-ui.panel>

<div class="flex flex-wrap gap-3 lg:col-span-2">
    <x-ui.button type="submit" icon="heroicon-m-check">Simpan</x-ui.button>
    <x-ui.button :href="route('admin.reels.index')" outlined color="gray">Batal</x-ui.button>
</div>
