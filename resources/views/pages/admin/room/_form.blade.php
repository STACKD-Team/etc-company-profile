@csrf
@if ($room->exists) @method('PUT') @endif

@php
    $mediaUrl = static function (?string $path): ?string {
        if (! $path) {
            return null;
        }

        return \Illuminate\Support\Str::startsWith($path, ['http://', 'https://', '/', 'images/', 'storage/'])
            ? asset(ltrim($path, '/'))
            : \Illuminate\Support\Facades\Storage::url($path);
    };
@endphp

<div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_320px]">
    <x-ui.panel heading="Informasi Room" description="Room dipakai sebagai lokasi kelas dan juga data fasilitas public.">
        <div class="grid gap-5 md:grid-cols-2">
            <div class="md:col-span-2">
                <x-ui.field name="name" label="Nama Room" :value="$room->name" required />
            </div>
            <x-ui.number-field name="capacity" label="Kapasitas" :value="$room->capacity" min="1" />
            <x-ui.number-field name="display_order" label="Urutan Tampil" :value="$room->display_order ?? 0" />
            <div class="md:col-span-2">
                <x-ui.textarea name="description" label="Deskripsi" rows="4" :value="$room->description" />
            </div>
            <div class="md:col-span-2">
                <x-ui.textarea
                    name="facilities_text"
                    label="Fasilitas"
                    rows="5"
                    helper="Tulis satu fasilitas per baris."
                    :value="old('facilities_text', implode(PHP_EOL, $room->facilities ?? []))"
                />
            </div>
            <x-ui.checkbox name="is_active" label="Aktif" :checked="(bool) ($room->is_active ?? true)" />
        </div>
    </x-ui.panel>

    <x-ui.panel heading="Media Room" description="Upload gambar room untuk halaman fasilitas.">
        <div class="overflow-hidden rounded-box border-2 border-etc-outline-variant/60 bg-etc-surface">
            @if ($room->image)
                <img src="{{ $mediaUrl($room->image) }}" alt="{{ $room->name }}" class="aspect-[4/3] w-full object-cover">
            @else
                <div class="flex aspect-[4/3] items-center justify-center text-etc-magenta">
                    <span class="material-symbols-outlined text-6xl">meeting_room</span>
                </div>
            @endif
        </div>
        <div class="mt-4">
            <x-ui.file-upload name="image" label="Gambar Room" accept="image/*" />
        </div>
    </x-ui.panel>

    <div class="flex flex-wrap gap-3 lg:col-span-2">
        <x-ui.button type="submit" icon="heroicon-m-check">Simpan</x-ui.button>
        <x-ui.button :href="$room->exists ? route('admin.room.show', $room) : route('admin.room.index')" outlined color="gray">Batal</x-ui.button>
    </div>
</div>
