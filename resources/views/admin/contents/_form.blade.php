@csrf
@if ($content->exists) @method('PUT') @endif

@php
    $mediaUrl = static function (?string $path): ?string {
        if (! $path) {
            return null;
        }

        return \Illuminate\Support\Str::startsWith($path, ['http://', 'https://', '/', 'images/', 'videos/', 'storage/'])
            ? asset(ltrim($path, '/'))
            : \Illuminate\Support\Facades\Storage::url($path);
    };
    $metaFields = [
        'value' => 'Nilai Setting',
        'capacity' => 'Kapasitas',
        'facility' => 'Fasilitas',
        'website' => 'Website',
        'since' => 'Sejak',
        'event_date' => 'Tanggal Event',
        'location' => 'Lokasi',
    ];
@endphp

<div class="grid gap-6 lg:grid-cols-[1fr_300px]">
    <div class="grid gap-5 md:grid-cols-2">
        <label>
            <span class="font-heading text-sm font-bold">Tipe</span>
            <select name="type" class="mt-2 min-h-12 w-full rounded-xl border border-etc-outline-variant px-4 text-sm focus:border-etc-magenta focus:outline-none">
                @foreach ($types as $type)
                    <option value="{{ $type }}" @selected(old('type', $content->type ?: 'page') === $type)>{{ str($type)->replace('_', ' ')->headline() }}</option>
                @endforeach
            </select>
            @error('type')<span class="mt-1 block text-xs font-bold text-red-600">{{ $message }}</span>@enderror
        </label>

        <label>
            <span class="font-heading text-sm font-bold">Slug</span>
            <input name="slug" value="{{ old('slug', $content->slug) }}" class="mt-2 min-h-12 w-full rounded-xl border border-etc-outline-variant px-4 text-sm focus:border-etc-magenta focus:outline-none">
            @error('slug')<span class="mt-1 block text-xs font-bold text-red-600">{{ $message }}</span>@enderror
        </label>

        <label class="md:col-span-2">
            <span class="font-heading text-sm font-bold">Judul</span>
            <input name="title" value="{{ old('title', $content->title) }}" class="mt-2 min-h-12 w-full rounded-xl border border-etc-outline-variant px-4 text-sm focus:border-etc-magenta focus:outline-none">
            @error('title')<span class="mt-1 block text-xs font-bold text-red-600">{{ $message }}</span>@enderror
        </label>

        <label class="md:col-span-2">
            <span class="font-heading text-sm font-bold">Isi</span>
            <textarea name="body" rows="6" class="mt-2 w-full rounded-xl border border-etc-outline-variant px-4 py-3 text-sm focus:border-etc-magenta focus:outline-none">{{ old('body', $content->body) }}</textarea>
            @error('body')<span class="mt-1 block text-xs font-bold text-red-600">{{ $message }}</span>@enderror
        </label>

        <label>
            <span class="font-heading text-sm font-bold">Gambar Utama</span>
            <input type="file" name="image" accept="image/*" class="mt-2 w-full rounded-xl border border-etc-outline-variant px-4 py-3 text-sm file:mr-4 file:rounded-pill file:border-0 file:bg-etc-charcoal file:px-4 file:py-2 file:font-heading file:text-xs file:font-bold file:text-white">
            @error('image')<span class="mt-1 block text-xs font-bold text-red-600">{{ $message }}</span>@enderror
        </label>

        <label>
            <span class="font-heading text-sm font-bold">Galeri Gambar</span>
            <input type="file" name="images[]" accept="image/*" multiple class="mt-2 w-full rounded-xl border border-etc-outline-variant px-4 py-3 text-sm file:mr-4 file:rounded-pill file:border-0 file:bg-etc-charcoal file:px-4 file:py-2 file:font-heading file:text-xs file:font-bold file:text-white">
            @error('images')<span class="mt-1 block text-xs font-bold text-red-600">{{ $message }}</span>@enderror
        </label>

        <label>
            <span class="font-heading text-sm font-bold">Urutan Tampil</span>
            <input type="number" name="display_order" value="{{ old('display_order', $content->display_order ?? 0) }}" class="mt-2 min-h-12 w-full rounded-xl border border-etc-outline-variant px-4 text-sm focus:border-etc-magenta focus:outline-none">
            @error('display_order')<span class="mt-1 block text-xs font-bold text-red-600">{{ $message }}</span>@enderror
        </label>

        <label class="flex items-center gap-3 rounded-xl bg-etc-surface-container px-4 py-3">
            <input type="checkbox" name="is_published" value="1" class="h-5 w-5 rounded border-etc-outline-variant text-etc-magenta" @checked(old('is_published', $content->is_published ?? true))>
            <span class="font-heading text-sm font-bold">Published</span>
        </label>

        <div class="md:col-span-2 rounded-card bg-etc-surface-container p-5">
            <h2 class="font-heading text-base font-black">Meta</h2>
            <div class="mt-4 grid gap-4 md:grid-cols-2">
                @foreach ($metaFields as $key => $label)
                    <label>
                        <span class="font-heading text-xs font-bold uppercase text-etc-on-muted">{{ $label }}</span>
                        <input name="meta[{{ $key }}]" value="{{ old('meta.'.$key, $content->meta[$key] ?? '') }}" class="mt-2 min-h-11 w-full rounded-xl border border-etc-outline-variant bg-white px-4 text-sm focus:border-etc-magenta focus:outline-none">
                    </label>
                @endforeach
            </div>
        </div>
    </div>

    <aside class="rounded-card border border-etc-outline-variant bg-etc-surface-container p-5">
        <p class="font-heading text-sm font-black uppercase text-etc-magenta">Media Saat Ini</p>
        <div class="mt-4 overflow-hidden rounded-2xl bg-white">
            @if ($content->image)
                <img src="{{ $mediaUrl($content->image) }}" alt="{{ $content->title }}" class="aspect-[4/3] w-full object-cover">
            @else
                <div class="flex aspect-[4/3] items-center justify-center text-etc-magenta">
                    <span class="material-symbols-outlined text-6xl">image</span>
                </div>
            @endif
        </div>
        @if (is_array($content->images) && $content->images !== [])
            <div class="mt-4 grid grid-cols-3 gap-2">
                @foreach ($content->images as $image)
                    <img src="{{ $mediaUrl($image) }}" alt="" class="aspect-square rounded-xl object-cover">
                @endforeach
            </div>
        @endif
    </aside>
</div>

<div class="mt-6 flex flex-wrap gap-3">
    <button class="inline-flex min-h-12 items-center gap-2 rounded-pill bg-etc-magenta px-6 font-heading text-sm font-bold text-white">
        <span class="material-symbols-outlined text-base">save</span>
        Simpan
    </button>
    <a href="{{ route('admin.contents.index') }}" class="inline-flex min-h-12 items-center rounded-pill border border-etc-outline-variant px-6 font-heading text-sm font-bold text-etc-charcoal">Batal</a>
</div>
