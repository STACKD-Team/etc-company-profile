<x-layouts.dashboard title="Rapor" area="student" active="reports" :user="$student">
    <x-ui.panel heading="Rapor Saya" description="Hanya rapor yang sudah dipublikasikan admin yang tampil di halaman siswa.">
        <div class="grid gap-5 md:grid-cols-2">
            @forelse ($reportCards as $reportCard)
                @php($class = $reportCard->enrollment?->courseClass)
                <article class="rounded-card border border-etc-outline-variant/70 bg-white p-6">
                    <div class="flex flex-wrap items-center gap-2">
                        <x-ui.badge status="published">Published</x-ui.badge>
                        <x-ui.badge status="success">Nilai {{ $reportCard->final_grade ?? '-' }}</x-ui.badge>
                    </div>
                    <p class="mt-4 font-heading text-xs font-bold uppercase text-etc-magenta">{{ $reportCard->issued_at?->format('d M Y') ?? 'Tanggal belum tersedia' }}</p>
                    <h2 class="mt-2 font-heading text-xl font-bold text-etc-on-surface">{{ $class?->program?->name ?? 'Program ETC Planet' }}</h2>
                    <p class="mt-2 text-sm text-etc-on-muted">{{ $class?->name ?? 'Kelas ETC' }} - {{ $class?->instructor?->full_name ?? $class?->instructor?->name ?? 'Instruktur belum ditentukan' }}</p>

                    <dl class="mt-5 grid grid-cols-2 gap-3">
                        <div class="rounded-card bg-etc-surface-low p-4">
                            <dt class="text-xs font-bold uppercase text-etc-on-muted">Total Score</dt>
                            <dd class="mt-1 font-heading text-2xl font-black text-etc-magenta">{{ $reportCard->total_score ?? '-' }}</dd>
                        </div>
                        <div class="rounded-card bg-etc-surface-low p-4">
                            <dt class="text-xs font-bold uppercase text-etc-on-muted">Next Class</dt>
                            <dd class="mt-1 font-heading text-sm font-bold text-etc-on-surface">{{ $reportCard->next_class ?? '-' }}</dd>
                        </div>
                    </dl>

                    <div class="mt-5 flex flex-wrap gap-3">
                        <x-ui.button :href="route('student.report-cards.show', $reportCard)" icon="heroicon-m-eye">Lihat Rapor</x-ui.button>
                        @if ($reportCard->pdf_path)
                            <x-ui.button :href="route('student.report-cards.download', $reportCard)" outlined icon="heroicon-m-arrow-down-tray">Unduh</x-ui.button>
                        @else
                            <x-ui.badge status="unpublished">File belum tersedia</x-ui.badge>
                        @endif
                    </div>
                </article>
            @empty
                <div class="md:col-span-2">
                    <x-ui.empty-state
                        heading="Belum ada rapor yang dipublikasikan"
                        description="Rapor akan tampil setelah admin mempublish hasil pembelajaran."
                        icon="heroicon-o-document-text"
                    />
                </div>
            @endforelse
        </div>
    </x-ui.panel>
</x-layouts.dashboard>
