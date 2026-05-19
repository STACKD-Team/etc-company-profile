<x-layouts.dashboard title="Data Instruktur" area="admin" active="instructors">
    <section class="rounded-card bg-white p-6 shadow-panel">
        <form method="GET" class="mb-5 flex gap-3">
            <input name="search" value="{{ request('search') }}" placeholder="Cari instruktur" class="min-h-11 flex-1 rounded-xl border border-etc-outline-variant px-4 text-sm">
            <button class="rounded-pill bg-etc-magenta px-5 font-heading text-sm font-bold text-white">Cari</button>
        </form>
        <div class="grid gap-4 md:grid-cols-2">
            @foreach ($instructors as $instructor)
                <a href="{{ route('admin.instructors.show', $instructor) }}" class="rounded-card border border-etc-outline-variant p-5 transition hover:border-etc-magenta">
                    <strong class="font-heading">{{ $instructor->full_name ?? $instructor->name }}</strong>
                    <p class="mt-2 text-sm text-etc-on-muted">{{ $instructor->instructor_specialization ?? 'Instruktur ETC Planet' }}</p>
                    <p class="mt-3 text-xs font-bold text-etc-magenta">{{ $instructor->classes_taught_count }} kelas</p>
                </a>
            @endforeach
        </div>
        <div class="mt-5">{{ $instructors->links() }}</div>
    </section>
</x-layouts.dashboard>
