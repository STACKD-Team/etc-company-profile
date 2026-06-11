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
    $typeOptions = collect($types)->mapWithKeys(fn ($type) => [$type => str($type)->replace('_', ' ')->headline()->toString()])->all();
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

<x-ui.panel heading="Detail Konten" description="Kelola data CMS tanpa mengubah struktur type, meta, atau media Sprint 1." class="min-w-0">
    <div class="grid gap-5 md:grid-cols-2">
        <x-ui.select name="type" label="Tipe" :value="$content->type ?: 'page'" :options="$typeOptions" required />
        <x-ui.field name="slug" label="Slug" :value="$content->slug" />

        <div class="md:col-span-2">
            <x-ui.field name="title" label="Judul" :value="$content->title" required />
        </div>

        <div class="md:col-span-2">
            <x-ui.textarea name="body" label="Isi" rows="6" :value="$content->body" />
        </div>

        <x-ui.file-upload name="image" label="Gambar Utama" accept="image/*" />
        <x-ui.file-upload name="images[]" label="Galeri Gambar" accept="image/*" multiple />
        <x-ui.number-field name="display_order" label="Urutan Tampil" :value="$content->display_order ?? 0" />

        <div class="self-end">
            <x-ui.checkbox name="is_published" label="Published" helper="Konten publish bisa ditampilkan di public page terkait." :checked="(bool) ($content->is_published ?? true)" />
        </div>

        <div class="md:col-span-2 border-t-2 border-etc-outline-variant/60 pt-5">
            <h2 class="font-heading text-base font-bold text-etc-on-surface">Meta</h2>
            <div class="mt-4 grid gap-4 md:grid-cols-2">
                @foreach ($metaFields as $key => $label)
                    <x-ui.field :name="'meta['.$key.']'" :label="$label" :value="old('meta.'.$key, $content->meta[$key] ?? '')" size="sm" />
                @endforeach
            </div>
        </div>
    </div>
</x-ui.panel>

<x-ui.panel heading="Media Saat Ini" description="Preview membantu admin memeriksa gambar aktif sebelum mengganti file." class="min-w-0">
    <div class="overflow-hidden rounded-box border-2 border-etc-outline-variant/60 bg-etc-surface">
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
                <img src="{{ $mediaUrl($image) }}" alt="" class="aspect-square rounded-selector object-cover">
            @endforeach
        </div>
    @endif
</x-ui.panel>

<div class="flex flex-wrap gap-3 lg:col-span-2">
    <x-ui.button type="submit" icon="heroicon-m-check">Simpan</x-ui.button>
    <x-ui.button :href="route('admin.contents.index')" outlined color="gray">Batal</x-ui.button>
</div>
