<x-layouts.dashboard title="Detail Siswa" area="admin" active="students">
    <div class="space-y-6">
        <x-ui.panel>
            <div class="flex flex-col justify-between gap-4 md:flex-row md:items-start">
                <div>
                    <p class="font-heading text-xs font-bold uppercase text-etc-magenta">Profil Siswa</p>
                    <h2 class="mt-2 font-heading text-2xl font-black text-etc-on-surface">{{ $student->full_name ?? $student->name }}</h2>
                    <p class="mt-2 text-sm text-etc-on-muted">{{ $student->email }} - {{ $student->mobile_phone ?? 'No HP belum diisi' }}</p>
                </div>
                <x-ui.badge :status="$student->is_active ? 'active' : 'inactive'">{{ $student->is_active ? 'Aktif' : 'Nonaktif' }}</x-ui.badge>
            </div>

            <dl class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                @foreach ([
                    'No Induk' => $student->no_induk ?: '-',
                    'Jenis Kelamin' => $student->sex ?: '-',
                    'TTL' => trim(($student->place_of_birth ?: '-').' / '.($student->date_of_birth?->format('d M Y') ?: '-')),
                    'Sekolah/Pekerjaan' => $student->occupation_school ?: '-',
                    'Alamat' => $student->address ?: '-',
                    'Orang Tua' => trim(($student->father_name ?: '-').' / '.($student->mother_name ?: '-')),
                    'NISN' => $student->nisn ?: '-',
                    'NIK' => $student->nik ?: '-',
                ] as $label => $value)
                    <div class="rounded-lg bg-etc-surface p-4">
                        <dt class="font-heading text-xs font-bold uppercase text-etc-on-muted">{{ $label }}</dt>
                        <dd class="mt-2 text-sm text-etc-on-surface">{{ $value }}</dd>
                    </div>
                @endforeach
            </dl>
        </x-ui.panel>

        <x-ui.panel heading="Histori Kelas" description="Bersumber dari enrollments agar admin melihat perjalanan belajar lengkap.">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[840px] text-left text-sm">
                    <thead>
                        <tr class="border-b border-etc-outline-variant/60 font-heading text-xs font-bold uppercase text-etc-on-muted">
                            <th class="py-3 pr-4">Program</th>
                            <th class="py-3 pr-4">Kelas</th>
                            <th class="py-3 pr-4">Instructor</th>
                            <th class="py-3 pr-4">Mulai</th>
                            <th class="py-3 pr-4">Selesai</th>
                            <th class="py-3 pr-4">Status</th>
                            <th class="py-3 pr-4">Rapor</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-etc-outline-variant/50">
                        @forelse ($student->enrollments as $enrollment)
                            <tr>
                                <td class="py-4 pr-4 text-etc-on-muted">{{ $enrollment->courseClass?->program?->name ?? '-' }}</td>
                                <td class="py-4 pr-4 font-heading font-bold text-etc-on-surface">{{ $enrollment->courseClass?->name ?? '-' }}</td>
                                <td class="py-4 pr-4 text-etc-on-muted">{{ $enrollment->courseClass?->instructor?->full_name ?? $enrollment->courseClass?->instructor?->name ?? '-' }}</td>
                                <td class="py-4 pr-4 text-etc-on-muted">{{ $enrollment->enrolled_at?->format('d M Y') ?? '-' }}</td>
                                <td class="py-4 pr-4 text-etc-on-muted">{{ $enrollment->completed_at?->format('d M Y') ?? '-' }}</td>
                                <td class="py-4 pr-4"><x-ui.badge :status="$enrollment->status" /></td>
                                <td class="py-4 pr-4">
                                    @if ($enrollment->reportCard)
                                        <x-ui.button :href="route('admin.report-cards.show', $enrollment->reportCard)" size="sm" outlined>Rapor</x-ui.button>
                                    @else
                                        <span class="text-xs font-bold text-etc-on-muted">Belum ada</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8">
                                    <x-ui.empty-state heading="Belum ada histori kelas" description="Histori akan muncul setelah siswa dimasukkan ke kelas." icon="heroicon-o-academic-cap" />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-ui.panel>
    </div>
</x-layouts.dashboard>
