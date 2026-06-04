<x-layouts.dashboard :title="$title" area="admin" active="reports">
    <section class="max-w-4xl rounded-card border border-etc-outline-variant/60 bg-white p-6 shadow-soft">
        <div>
            <p class="font-heading text-xs font-bold uppercase text-etc-magenta">Template Export</p>
            <h2 class="mt-2 font-heading text-2xl font-black text-etc-on-surface">{{ $title }}</h2>
            <p class="mt-3 text-sm leading-6 text-etc-on-muted">{{ $description }}</p>
        </div>

        <form method="POST" action="{{ $action }}" class="mt-6 space-y-5">
            @csrf

            @if (($type ?? null) === 'students')
                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label for="year" class="font-heading text-sm font-bold text-etc-on-surface">Tahun daftar</label>
                        <input id="year" name="year" type="number" min="2020" max="2100" value="{{ now()->year }}" class="mt-2 min-h-12 w-full rounded-lg border border-etc-outline-variant px-4 py-3">
                    </div>
                    <div>
                        <label for="status" class="font-heading text-sm font-bold text-etc-on-surface">Status siswa</label>
                        <input id="status" name="status" placeholder="Pelajar, Mahasiswa, Karyawan" class="mt-2 min-h-12 w-full rounded-lg border border-etc-outline-variant px-4 py-3">
                    </div>
                    <div>
                        <label for="program_id" class="font-heading text-sm font-bold text-etc-on-surface">Program</label>
                        <select id="program_id" name="program_id" class="mt-2 min-h-12 w-full rounded-lg border border-etc-outline-variant px-4 py-3">
                            <option value="">Semua program</option>
                            @foreach ($programs as $program)
                                <option value="{{ $program->id }}">{{ $program->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="class_id" class="font-heading text-sm font-bold text-etc-on-surface">Kelas</label>
                        <select id="class_id" name="class_id" class="mt-2 min-h-12 w-full rounded-lg border border-etc-outline-variant px-4 py-3">
                            <option value="">Semua kelas</option>
                            @foreach ($classes as $class)
                                <option value="{{ $class->id }}">{{ $class->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            @else
                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label for="report_card_id" class="font-heading text-sm font-bold text-etc-on-surface">Rapor</label>
                        <select id="report_card_id" name="report_card_id" class="mt-2 min-h-12 w-full rounded-lg border border-etc-outline-variant px-4 py-3">
                            <option value="">Semua rapor</option>
                            @foreach ($reportCards as $reportCard)
                                <option value="{{ $reportCard->id }}">{{ $reportCard->enrollment?->user?->full_name ?? $reportCard->enrollment?->user?->name ?? 'Rapor #'.$reportCard->id }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="class_id" class="font-heading text-sm font-bold text-etc-on-surface">Kelas</label>
                        <select id="class_id" name="class_id" class="mt-2 min-h-12 w-full rounded-lg border border-etc-outline-variant px-4 py-3">
                            <option value="">Semua kelas</option>
                            @foreach ($classes as $class)
                                <option value="{{ $class->id }}">{{ $class->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="is_published" class="font-heading text-sm font-bold text-etc-on-surface">Status publish</label>
                        <select id="is_published" name="is_published" class="mt-2 min-h-12 w-full rounded-lg border border-etc-outline-variant px-4 py-3">
                            <option value="">Semua</option>
                            <option value="1">Published</option>
                            <option value="0">Draft</option>
                        </select>
                    </div>
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label for="issued_from" class="font-heading text-sm font-bold text-etc-on-surface">Terbit dari</label>
                            <input id="issued_from" name="issued_from" type="date" class="mt-2 min-h-12 w-full rounded-lg border border-etc-outline-variant px-4 py-3">
                        </div>
                        <div>
                            <label for="issued_to" class="font-heading text-sm font-bold text-etc-on-surface">Terbit sampai</label>
                            <input id="issued_to" name="issued_to" type="date" class="mt-2 min-h-12 w-full rounded-lg border border-etc-outline-variant px-4 py-3">
                        </div>
                    </div>
                </div>
            @endif

            <button type="submit" class="inline-flex min-h-12 items-center justify-center gap-2 rounded-full bg-etc-magenta px-5 py-3 font-heading text-sm font-bold text-white transition hover:bg-etc-primary">
                <span class="material-symbols-outlined text-lg">download</span>
                Download
            </button>
        </form>
    </section>
</x-layouts.dashboard>
