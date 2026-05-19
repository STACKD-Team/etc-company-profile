<x-layouts.dashboard title="Detail Kelas" area="student" active="classes" :user="$student">
    <article class="rounded-card bg-white p-6 shadow-panel">
        <p class="text-xs font-bold uppercase text-etc-magenta">{{ $enrollment->status }}</p>
        <h2 class="mt-2 font-heading text-2xl font-black text-etc-on-surface">{{ $class->program?->name }} - {{ $class->name }}</h2>
        <dl class="mt-6 grid gap-4 md:grid-cols-2">
            <div><dt class="text-sm font-bold text-etc-on-muted">Instruktur</dt><dd class="mt-1">{{ $class->instructor?->full_name ?? $class->instructor?->name ?? '-' }}</dd></div>
            <div><dt class="text-sm font-bold text-etc-on-muted">Jadwal</dt><dd class="mt-1">{{ $class->schedule_days ?? '-' }} {{ $class->schedule_time ?? '' }}</dd></div>
            <div><dt class="text-sm font-bold text-etc-on-muted">Ruangan</dt><dd class="mt-1">{{ $class->room ?? '-' }}</dd></div>
            <div><dt class="text-sm font-bold text-etc-on-muted">Mulai</dt><dd class="mt-1">{{ $class->start_date?->format('d M Y') ?? '-' }}</dd></div>
        </dl>
    </article>
</x-layouts.dashboard>
