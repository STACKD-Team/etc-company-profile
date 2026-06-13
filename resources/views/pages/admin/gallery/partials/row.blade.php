@php
    $content = $item;
    $mediaUrl = static function (?string $path): ?string {
        if (! $path) {
            return null;
        }

        return \Illuminate\Support\Str::startsWith($path, ['http://', 'https://', '/', 'images/', 'videos/', 'storage/'])
            ? asset(ltrim($path, '/'))
            : \Illuminate\Support\Facades\Storage::url($path);
    };
@endphp

<tr>
    <td class="py-4 pr-4">
        <div class="flex min-w-[260px] items-center gap-3">
            <div class="flex h-14 w-14 shrink-0 items-center justify-center overflow-hidden rounded-lg bg-etc-surface-container text-etc-magenta">
                @if ($content->image)
                    <img src="{{ $mediaUrl($content->image) }}" alt="{{ $content->title }}" class="h-full w-full object-cover">
                @else
                    <span class="material-symbols-outlined text-2xl">dashboard_customize</span>
                @endif
            </div>
            <div class="min-w-0">
                <p class="truncate font-heading font-bold text-etc-on-surface">{{ $content->title }}</p>
                <p class="mt-1 truncate text-xs text-etc-on-muted">{{ $content->slug ?: '-' }}</p>
            </div>
        </div>
    </td>
    <td class="py-4 pr-4"><x-ui.badge status="primary">{{ str($content->type)->replace('_', ' ')->headline() }}</x-ui.badge></td>
    <td class="py-4 pr-4 font-heading font-bold text-etc-on-surface">{{ (int) $content->display_order }}</td>
    <td class="py-4 pr-4"><x-ui.badge :status="$content->is_published ? 'published' : 'draft'">{{ $content->is_published ? 'Published' : 'Draft' }}</x-ui.badge></td>
    <td class="py-4 pr-4 text-etc-on-muted">{{ $content->updated_at?->format('d M Y H:i') ?? '-' }}</td>
    <td class="py-4 pr-4">
        <div class="flex flex-wrap gap-2">
            <x-ui.button :href="route('admin.'.$content->type.'.show', $content)" size="sm" outlined>Detail</x-ui.button>
            <x-ui.button :href="route('admin.'.$content->type.'.edit', $content)" size="sm" color="gray" outlined>Edit</x-ui.button>
        </div>
    </td>
</tr>
