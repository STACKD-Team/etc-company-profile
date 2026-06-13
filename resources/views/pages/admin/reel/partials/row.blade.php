@php
    $reel = $item;
    $mediaUrl = static function (?string $path, ?string $fallback = null): ?string {
        if (! $path) {
            return $fallback ? asset($fallback) : null;
        }

        return \Illuminate\Support\Str::startsWith($path, ['http://', 'https://', '/', 'images/', 'videos/', 'storage/'])
            ? asset(ltrim($path, '/'))
            : \Illuminate\Support\Facades\Storage::url($path);
    };
@endphp

<tr>
    <td class="py-4 pr-4">
        <div class="flex min-w-[260px] items-center gap-3">
            <div class="flex h-16 w-12 shrink-0 items-center justify-center overflow-hidden rounded-lg bg-etc-charcoal text-white/70">
                @if ($reel->thumbnail_path || $reel->video_path)
                    <video muted playsinline preload="metadata" poster="{{ $mediaUrl($reel->thumbnail_path, 'images/pu1-img (3).jpg') }}" class="h-full w-full object-cover">
                        <source src="{{ $mediaUrl($reel->video_path, 'videos/video1.mp4') }}" type="video/mp4">
                    </video>
                @else
                    <span class="material-symbols-outlined">smart_display</span>
                @endif
            </div>
            <div class="min-w-0">
                <p class="truncate font-heading font-bold text-etc-on-surface">{{ $reel->title }}</p>
                <p class="mt-1 line-clamp-1 text-xs text-etc-on-muted">{{ $reel->description ?: 'Tidak ada deskripsi.' }}</p>
            </div>
        </div>
    </td>
    <td class="py-4 pr-4"><x-ui.badge status="primary">{{ ucfirst($reel->category ?: 'edukasi') }}</x-ui.badge></td>
    <td class="py-4 pr-4 font-heading font-bold text-etc-on-surface">{{ number_format((int) $reel->views_count) }}</td>
    <td class="py-4 pr-4 font-heading font-bold text-etc-magenta">{{ number_format((int) $reel->likes_count) }}</td>
    <td class="py-4 pr-4"><x-ui.badge :status="$reel->is_published ? 'published' : 'draft'">{{ $reel->is_published ? 'Published' : 'Draft' }}</x-ui.badge></td>
    <td class="py-4 pr-4 text-etc-on-muted">{{ $reel->created_at?->format('d M Y') ?? '-' }}</td>
    <td class="py-4 pr-4">
        <div class="flex flex-wrap gap-2">
            <x-ui.button :href="route('admin.reel.show', $reel)" size="sm" outlined>Detail</x-ui.button>
            <x-ui.button :href="route('admin.reel.edit', $reel)" size="sm" color="gray" outlined>Edit</x-ui.button>
        </div>
    </td>
</tr>
