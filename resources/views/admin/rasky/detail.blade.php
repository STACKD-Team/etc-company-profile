<x-layouts.dashboard :title="$title" :area="$area ?? 'admin'" :active="$active ?? null">
    @if (session('status'))
        <div class="mb-5 rounded-lg bg-etc-surface-container p-4 text-sm text-etc-on-surface">{{ session('status') }}</div>
    @endif

    <section class="rounded-card bg-white p-6 shadow-soft">
        <h2 class="font-heading text-2xl font-black text-etc-on-surface">{{ $heading }}</h2>
        <p class="mt-2 text-sm text-etc-on-muted">{{ $description }}</p>

        <dl class="mt-6 grid gap-4 md:grid-cols-2">
            @foreach ($details as $label => $value)
                <div class="rounded-lg bg-etc-surface p-4">
                    <dt class="font-heading text-xs font-bold uppercase text-etc-on-muted">{{ $label }}</dt>
                    <dd class="mt-2 text-sm text-etc-on-surface">{{ $value }}</dd>
                </div>
            @endforeach
        </dl>
    </section>
</x-layouts.dashboard>
