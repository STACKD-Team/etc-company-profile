<x-layouts.dashboard :title="$title" area="admin" active="reports">
    <section class="max-w-2xl rounded-card bg-white p-6 shadow-soft">
        <h2 class="font-heading text-2xl font-black text-etc-on-surface">{{ $title }}</h2>
        <p class="mt-3 text-sm leading-6 text-etc-on-muted">{{ $description }}</p>

        <form method="POST" action="{{ $action }}" class="mt-6">
            @csrf
            <button type="submit" class="inline-flex min-h-12 items-center justify-center rounded-full bg-etc-magenta px-5 py-3 font-heading text-sm font-bold text-white transition hover:bg-etc-primary">
                Download
            </button>
        </form>
    </section>
</x-layouts.dashboard>
