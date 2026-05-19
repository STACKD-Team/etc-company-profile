<x-layouts.dashboard title="Master Program" area="admin" active="programs">
    <section class="rounded-card bg-white p-6 shadow-panel">
        @if (session('status'))<div class="mb-4 rounded-card bg-green-50 p-3 text-sm font-bold text-green-700">{{ session('status') }}</div>@endif
        <div class="mb-5 flex items-center justify-between gap-4">
            <form method="GET" class="flex flex-1 gap-3"><input name="search" value="{{ request('search') }}" placeholder="Cari program" class="min-h-11 flex-1 rounded-xl border border-etc-outline-variant px-4 text-sm"><button class="rounded-pill bg-etc-charcoal px-5 text-sm font-bold text-white">Cari</button></form>
            <a href="{{ route('admin.programs.create') }}" class="rounded-pill bg-etc-magenta px-5 py-3 text-sm font-bold text-white">Tambah</a>
        </div>
        <div class="grid gap-4 md:grid-cols-2">
            @foreach ($programs as $program)
                <article class="rounded-card border border-etc-outline-variant p-5">
                    <p class="text-xs font-bold uppercase text-etc-magenta">{{ $program->category }}</p>
                    <h2 class="mt-2 font-heading text-lg font-bold">{{ $program->name }}</h2>
                    <p class="mt-2 text-sm text-etc-on-muted">{{ $program->duration_meetings }} pertemuan • Rp {{ number_format((int) $program->price, 0, ',', '.') }}</p>
                    <a href="{{ route('admin.programs.edit', $program) }}" class="mt-4 inline-block font-bold text-etc-magenta">Edit</a>
                </article>
            @endforeach
        </div>
        <div class="mt-5">{{ $programs->links() }}</div>
    </section>
</x-layouts.dashboard>
