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
    $isFaq = $contentType === \App\Models\Content::TYPE_FAQ;
    $isTestimonial = $contentType === \App\Models\Content::TYPE_TESTIMONIAL;
    $isPartner = $contentType === \App\Models\Content::TYPE_PARTNER;
@endphp

<input type="hidden" name="type" value="{{ $contentType }}">

<x-ui.panel :heading="'Detail '.$pageTitle" description="Kelola konten public dengan form yang sederhana untuk admin." class="min-w-0">
    <div class="grid gap-5 md:grid-cols-2">
        <div class="md:col-span-2">
            <x-ui.field name="title" :label="$isFaq ? 'Pertanyaan' : ($isTestimonial ? 'Nama' : 'Judul')" :value="$content->title" required />
        </div>

        <div class="md:col-span-2">
            <x-ui.textarea name="body" :label="$isFaq ? 'Jawaban' : ($isTestimonial ? 'Pesan' : 'Deskripsi')" rows="6" :value="$content->body" />
        </div>

        @unless ($isFaq)
            <x-ui.file-upload name="image" :label="$isPartner ? 'Logo' : ($isTestimonial ? 'Foto' : 'Gambar Utama')" accept="image/*" />
        @endunless

        @if ($contentType === \App\Models\Content::TYPE_GALLERY)
            <x-ui.file-upload name="images[]" label="Galeri Gambar" accept="image/*" multiple />
        @endif

        <x-ui.number-field name="display_order" label="Urutan Tampil" :value="$content->display_order ?? 0" />

        <div class="self-end">
            <x-ui.checkbox name="is_published" label="Published" helper="Konten publish bisa ditampilkan di public page terkait." :checked="(bool) ($content->is_published ?? true)" />
        </div>

        @if ($metaFields !== [])
            <div class="md:col-span-2 border-t-2 border-etc-outline-variant/60 pt-5">
                <h2 class="font-heading text-base font-bold text-etc-on-surface">Informasi Tambahan</h2>
            <div class="mt-4 grid gap-4 md:grid-cols-2">
                @foreach ($metaFields as $key => $label)
                    @if ($key === 'rating')
                        <x-ui.number-field :name="'meta['.$key.']'" :label="$label" :value="old('meta.'.$key, $content->meta[$key] ?? 5)" min="1" max="5" required />
                    @elseif ($key === 'website')
                        <x-ui.field :name="'meta['.$key.']'" :label="$label" type="url" :value="old('meta.'.$key, $content->meta[$key] ?? '')" size="sm" />
                    @else
                        <x-ui.field :name="'meta['.$key.']'" :label="$label" :value="old('meta.'.$key, $content->meta[$key] ?? '')" size="sm" />
                    @endif
                @endforeach
            </div>
            </div>
        @endif
    </div>
</x-ui.panel>

@unless ($isFaq)
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
@endunless

<div class="flex flex-wrap gap-3 lg:col-span-2">
    <x-ui.button type="submit" icon="heroicon-m-check">Simpan</x-ui.button>
    <x-ui.button :href="route($routeBase.'.index')" outlined color="gray">Batal</x-ui.button>
</div>
