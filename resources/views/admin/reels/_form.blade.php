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
@endphp

<div class="grid gap-6 lg:grid-cols-[1fr_280px]">
    <div class="grid gap-5 md:grid-cols-2">
        <label class="md:col-span-2">
            <span class="font-heading text-sm font-bold">Judul</span>
            <input name="title" value="{{ old('title', $reel->title) }}" class="mt-2 min-h-12 w-full rounded-xl border border-etc-outline-variant px-4 text-sm focus:border-etc-magenta focus:outline-none">
            @error('title')<span class="mt-1 block text-xs font-bold text-red-600">{{ $message }}</span>@enderror
        </label>

        <label>
            <span class="font-heading text-sm font-bold">Kategori</span>
            <select name="category" class="mt-2 min-h-12 w-full rounded-xl border border-etc-outline-variant px-4 text-sm focus:border-etc-magenta focus:outline-none">
                @foreach ($categories as $category)
                    <option value="{{ $category }}" @selected(old('category', $reel->category ?: 'edukasi') === $category)>{{ ucfirst($category) }}</option>
                @endforeach
            </select>
            @error('category')<span class="mt-1 block text-xs font-bold text-red-600">{{ $message }}</span>@enderror
        </label>

        <label>
            <span class="font-heading text-sm font-bold">Durasi Detik</span>
            <input type="number" min="1" name="duration_seconds" value="{{ old('duration_seconds', $reel->duration_seconds) }}" class="mt-2 min-h-12 w-full rounded-xl border border-etc-outline-variant px-4 text-sm focus:border-etc-magenta focus:outline-none">
            @error('duration_seconds')<span class="mt-1 block text-xs font-bold text-red-600">{{ $message }}</span>@enderror
        </label>

        <label class="md:col-span-2">
            <span class="font-heading text-sm font-bold">Deskripsi</span>
            <textarea name="description" rows="5" class="mt-2 w-full rounded-xl border border-etc-outline-variant px-4 py-3 text-sm focus:border-etc-magenta focus:outline-none">{{ old('description', $reel->description) }}</textarea>
            @error('description')<span class="mt-1 block text-xs font-bold text-red-600">{{ $message }}</span>@enderror
        </label>

        <label>
            <span class="font-heading text-sm font-bold">Video</span>
            <input type="file" name="video" accept="video/mp4,video/quicktime,video/webm" class="mt-2 w-full rounded-xl border border-etc-outline-variant px-4 py-3 text-sm file:mr-4 file:rounded-pill file:border-0 file:bg-etc-charcoal file:px-4 file:py-2 file:font-heading file:text-xs file:font-bold file:text-white">
            @error('video')<span class="mt-1 block text-xs font-bold text-red-600">{{ $message }}</span>@enderror
        </label>

        <label>
            <span class="font-heading text-sm font-bold">Thumbnail</span>
            <input type="file" name="thumbnail" accept="image/*" class="mt-2 w-full rounded-xl border border-etc-outline-variant px-4 py-3 text-sm file:mr-4 file:rounded-pill file:border-0 file:bg-etc-charcoal file:px-4 file:py-2 file:font-heading file:text-xs file:font-bold file:text-white">
            @error('thumbnail')<span class="mt-1 block text-xs font-bold text-red-600">{{ $message }}</span>@enderror
        </label>

        <label class="flex items-center gap-3 rounded-xl bg-etc-surface-container px-4 py-3 md:col-span-2">
            <input type="checkbox" name="is_published" value="1" class="h-5 w-5 rounded border-etc-outline-variant text-etc-magenta" @checked(old('is_published', $reel->is_published))>
            <span class="font-heading text-sm font-bold">Published</span>
        </label>
    </div>

    <aside class="rounded-card bg-etc-charcoal p-5 text-white">
        <p class="font-heading text-sm font-black uppercase text-etc-magenta">Preview</p>
        <div class="mt-4 overflow-hidden rounded-[22px] bg-black">
            @if ($reel->video_path)
                <video controls muted playsinline poster="{{ $mediaUrl($reel->thumbnail_path) }}" class="aspect-[9/14] w-full object-cover">
                    <source src="{{ $mediaUrl($reel->video_path) }}" type="video/mp4">
                </video>
            @else
                <div class="flex aspect-[9/14] items-center justify-center text-white/50">
                    <span class="material-symbols-outlined text-6xl">smart_display</span>
                </div>
            @endif
        </div>
        <p class="mt-4 text-sm text-white/60">{{ $reel->video_path ?: 'Video baru akan tampil setelah disimpan.' }}</p>
    </aside>
</div>

<div class="mt-6 flex flex-wrap gap-3">
    <button class="inline-flex min-h-12 items-center gap-2 rounded-pill bg-etc-magenta px-6 font-heading text-sm font-bold text-white">
        <span class="material-symbols-outlined text-base">save</span>
        Simpan
    </button>
    <a href="{{ route('admin.reels.index') }}" class="inline-flex min-h-12 items-center rounded-pill border border-etc-outline-variant px-6 font-heading text-sm font-bold text-etc-charcoal">Batal</a>
</div>
