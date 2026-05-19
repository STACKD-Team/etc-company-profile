<x-layouts.dashboard title="Rapor" area="student" active="reports" :user="$student">
    <div class="grid gap-5 md:grid-cols-2">
        @forelse ($reportCards as $reportCard)
            <article class="rounded-card bg-white p-6 shadow-soft">
                <p class="text-xs font-bold uppercase text-etc-magenta">{{ $reportCard->issued_at?->format('d M Y') ?? 'Rapor' }}</p>
                <h2 class="mt-2 font-heading text-xl font-bold">{{ $reportCard->enrollment?->courseClass?->program?->name ?? 'Program ETC Planet' }}</h2>
                <p class="mt-3 text-sm text-etc-on-muted">Nilai akhir: <strong>{{ $reportCard->final_grade ?? '-' }}</strong></p>
                <div class="mt-5 flex gap-3">
                    <a href="{{ route('student.report-cards.show', $reportCard) }}" class="rounded-pill bg-etc-magenta px-4 py-2 font-heading text-sm font-bold text-white">Lihat</a>
                    @if ($reportCard->pdf_path)
                        <a href="{{ route('student.report-cards.download', $reportCard) }}" class="rounded-pill border border-etc-outline-variant px-4 py-2 font-heading text-sm font-bold text-etc-on-muted">Unduh</a>
                    @endif
                </div>
            </article>
        @empty
            <div class="rounded-card bg-white p-8 text-center shadow-soft md:col-span-2">Belum ada rapor yang dipublikasikan.</div>
        @endforelse
    </div>
</x-layouts.dashboard>
