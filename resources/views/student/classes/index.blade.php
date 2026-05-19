<x-layouts.dashboard title="Kelas Saya" area="student" active="classes" :user="$student">
    <div class="grid gap-5">
        @forelse ($enrollments as $enrollment)
            @php($class = $enrollment->courseClass)
            <article class="rounded-card bg-white p-6 shadow-soft">
                <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase text-etc-magenta">{{ $enrollment->status }}</p>
                        <h2 class="mt-2 font-heading text-xl font-bold text-etc-on-surface">{{ $class?->program?->name }} - {{ $class?->name }}</h2>
                        <p class="mt-2 text-sm text-etc-on-muted">{{ $class?->schedule_days ?? 'Jadwal belum ditentukan' }} {{ $class?->schedule_time ? '• '.$class->schedule_time : '' }}</p>
                    </div>
                    @if ($class)
                        <a href="{{ route('student.classes.show', $class) }}" class="rounded-pill border border-etc-outline-variant px-4 py-2 font-heading text-sm font-bold text-etc-on-muted hover:border-etc-magenta hover:text-etc-magenta">Detail</a>
                    @endif
                </div>
            </article>
        @empty
            <div class="rounded-card bg-white p-8 text-center shadow-soft">Belum ada kelas.</div>
        @endforelse
    </div>
</x-layouts.dashboard>
