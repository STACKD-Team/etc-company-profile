<x-layouts.dashboard title="Detail Rapor" area="student" active="reports" :user="$student">
    <article class="rounded-card bg-white p-6 shadow-panel">
        <h2 class="font-heading text-2xl font-black">{{ $reportCard->enrollment?->courseClass?->program?->name ?? 'Rapor ETC Planet' }}</h2>
        <p class="mt-2 text-sm text-etc-on-muted">Diterbitkan: {{ $reportCard->issued_at?->format('d M Y') ?? '-' }}</p>
        <dl class="mt-6 grid gap-4 md:grid-cols-3">
            @foreach (['score_listening' => 'Listening', 'score_vocabulary' => 'Vocabulary', 'score_structure' => 'Structure', 'score_reading' => 'Reading', 'score_writing' => 'Writing', 'total_score' => 'Total'] as $field => $label)
                <div class="rounded-card bg-etc-surface-low p-4">
                    <dt class="text-sm font-bold text-etc-on-muted">{{ $label }}</dt>
                    <dd class="mt-1 font-heading text-2xl font-black text-etc-magenta">{{ $reportCard->{$field} ?? '-' }}</dd>
                </div>
            @endforeach
        </dl>
        <div class="mt-6 rounded-card bg-etc-surface-low p-4">
            <p class="text-sm font-bold text-etc-on-muted">Komentar</p>
            <p class="mt-2 text-sm leading-6">{{ $reportCard->comments ?? 'Belum ada komentar.' }}</p>
        </div>
    </article>
</x-layouts.dashboard>
