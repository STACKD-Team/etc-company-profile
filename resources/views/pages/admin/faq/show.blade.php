<x-layouts.dashboard :title="$pageTitle.' Detail'" area="admin" :active="$contentType">
    @php
        $mediaUrl = static function (?string $path): ?string {
            if (! $path) {
                return null;
            }

            return \Illuminate\Support\Str::startsWith($path, ['http://', 'https://', '/', 'images/', 'videos/', 'storage/'])
                ? asset(ltrim($path, '/'))
                : \Illuminate\Support\Facades\Storage::url($path);
        };

        $details = [];

        if ($contentType === \App\Models\Content::TYPE_PARTNER) {
            $details = [
                'Kategori' => $content->meta['category'] ?? null,
                'Website' => $content->meta['website'] ?? null,
                'Tahun kerja sama' => $content->meta['since'] ?? null,
            ];
        } elseif ($contentType === \App\Models\Content::TYPE_TESTIMONIAL) {
            $details = [
                'Role / asal' => $content->meta['role'] ?? null,
                'Rating' => isset($content->meta['rating']) ? $content->meta['rating'].'/5' : null,
            ];
        }

        $details = collect($details)->filter(fn ($value) => filled($value));
    @endphp

    @if (session('status'))
        <x-ui.alert status="success" class="mb-5">{{ session('status') }}</x-ui.alert>
    @endif

    <x-ui.resource-header
        :title="$content->title"
        :subtitle="'Detail '.$pageTitle"
        :back-url="route($routeBase.'.index')"
    >
        <x-slot:status>
            <x-ui.badge :status="$content->is_published ? 'published' : 'draft'">{{ $content->is_published ? 'Published' : 'Draft' }}</x-ui.badge>
        </x-slot:status>
        <x-slot:actions>
            <x-ui.button :href="route($routeBase.'.edit', $content)" icon="heroicon-m-pencil-square">Edit</x-ui.button>
            <x-ui.delete-action :action="route($routeBase.'.destroy', $content)" :heading="'Hapus '.$pageTitle.'?'" />
        </x-slot:actions>
    </x-ui.resource-header>

    <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_340px]">
        <x-ui.detail-card :heading="'Detail '.$pageTitle">
            <x-ui.description-list>
                <x-ui.description-item label="Judul" :value="$content->title" />
                <x-ui.description-item label="Urutan" :value="(string) $content->display_order" />
                <x-ui.description-item label="Status">
                    <x-ui.badge :status="$content->is_published ? 'published' : 'draft'">{{ $content->is_published ? 'Published' : 'Draft' }}</x-ui.badge>
                </x-ui.description-item>
                <x-ui.description-item label="Update" :value="$content->updated_at?->format('d M Y H:i') ?: '-'" />
            </x-ui.description-list>

            <div class="mt-5">
                <h2 class="font-heading text-sm font-bold text-etc-on-surface">{{ $contentType === \App\Models\Content::TYPE_FAQ ? 'Jawaban' : 'Konten' }}</h2>
                <p class="mt-2 whitespace-pre-line text-sm leading-6 text-etc-on-muted">{{ $content->body ?: '-' }}</p>
            </div>

            @if ($details->isNotEmpty())
                <div class="mt-5 border-t-2 border-etc-outline-variant/60 pt-5">
                    <h2 class="font-heading text-sm font-bold text-etc-on-surface">Informasi Tambahan</h2>
                    <x-ui.description-list class="mt-4">
                        @foreach ($details as $label => $value)
                            <x-ui.description-item :label="$label" :value="$value" />
                        @endforeach
                    </x-ui.description-list>
                </div>
            @endif
        </x-ui.detail-card>

        <x-ui.detail-card heading="Media">
            @if ($content->image)
                <img src="{{ $mediaUrl($content->image) }}" alt="{{ $content->title }}" class="aspect-[4/3] w-full rounded-box object-cover">
            @else
                <div class="flex aspect-[4/3] items-center justify-center rounded-box bg-etc-surface-container text-etc-magenta">
                    <span class="material-symbols-outlined text-6xl">image</span>
                </div>
            @endif

            @if (is_array($content->images) && $content->images !== [])
                <div class="mt-4 grid grid-cols-3 gap-2">
                    @foreach ($content->images as $image)
                        <img src="{{ $mediaUrl($image) }}" alt="" class="aspect-square rounded-selector object-cover">
                    @endforeach
                </div>
            @endif
        </x-ui.detail-card>
    </div>
</x-layouts.dashboard>
