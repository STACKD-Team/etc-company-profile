<x-layouts.dashboard title="Detail Instruktur" area="admin" active="instructors">
    <div class="space-y-6">
        <x-ui.panel>
            <div class="flex flex-col justify-between gap-4 md:flex-row md:items-start">
                <div>
                    <p class="font-heading text-xs font-bold uppercase text-etc-magenta">Instruktur</p>
                    <h2 class="mt-2 font-heading text-2xl font-black text-etc-on-surface">{{ $instructor->full_name ?? $instructor->name }}</h2>
                    <p class="mt-2 text-sm text-etc-on-muted">{{ $instructor->email }} - {{ $instructor->instructor_specialization ?? 'Spesialisasi belum diisi' }}</p>
                </div>
                <x-ui.badge :status="$instructor->show_on_team_page ? 'published' : 'draft'">{{ $instructor->show_on_team_page ? 'Tampil di Team' : 'Internal' }}</x-ui.badge>
            </div>

            <div class="mt-6 grid gap-4 md:grid-cols-2">
                <div class="rounded-lg bg-etc-surface p-4">
                    <p class="font-heading text-xs font-bold uppercase text-etc-on-muted">Posisi</p>
                    <p class="mt-2 text-sm text-etc-on-surface">{{ $instructor->instructor_position ?: '-' }}</p>
                </div>
                <div class="rounded-lg bg-etc-surface p-4">
                    <p class="font-heading text-xs font-bold uppercase text-etc-on-muted">Bio</p>
                    <p class="mt-2 text-sm leading-6 text-etc-on-surface">{{ $instructor->instructor_bio ?: 'Belum ada bio.' }}</p>
                </div>
            </div>
        </x-ui.panel>

        <x-ui.panel heading="Kelas Diampu">
            <div class="grid gap-3">
                @forelse ($instructor->classesTaught as $class)
                    <div class="flex flex-col justify-between gap-3 rounded-lg border border-etc-outline-variant/70 p-4 md:flex-row md:items-center">
                        <div>
                            <p class="font-heading font-bold text-etc-on-surface">{{ $class->name }}</p>
                            <p class="mt-1 text-sm text-etc-on-muted">{{ $class->program?->name ?? '-' }} - {{ $class->schedule_days ?? '-' }} {{ $class->schedule_time ?? '' }}</p>
                        </div>
                        <x-ui.badge :status="$class->status" />
                    </div>
                @empty
                    <x-ui.empty-state heading="Belum mengajar kelas" description="Kelas akan tampil setelah instructor di-assign ke kelas." icon="heroicon-o-academic-cap" />
                @endforelse
            </div>
        </x-ui.panel>
    </div>
</x-layouts.dashboard>
