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

            <dl class="mt-6 grid gap-x-6 border-t-2 border-etc-outline-variant/60 md:grid-cols-2 xl:grid-cols-4">
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
                    <div class="border-b-2 border-etc-outline-variant/60 py-4">
                        <dt class="font-heading text-xs font-bold uppercase text-etc-on-muted">{{ $label }}</dt>
                        <dd class="mt-2 text-sm text-etc-on-surface">{{ $value }}</dd>
                    </div>
                @endforeach
            </dl>
        </x-ui.panel>

        <div>
            <div class="mb-4">
                <h2 class="font-heading text-xl font-bold text-etc-on-surface">Histori Kelas</h2>
                <p class="mt-1 text-sm text-etc-on-muted">Bersumber dari enrollments agar admin melihat perjalanan belajar lengkap.</p>
            </div>
            <x-ui.data-table
                :items="$student->enrollments"
                :columns="[
                    'program' => 'Program',
                    'class' => 'Kelas',
                    'instructor' => 'Instructor',
                    'enrolled_at' => 'Mulai',
                    'completed_at' => 'Selesai',
                    'status' => 'Status',
                    'report' => 'Rapor',
                ]"
                row-view="admin.students.partials.enrollment-row"
                empty="Belum ada histori kelas"
                empty-description="Histori akan muncul setelah siswa dimasukkan ke kelas."
                :show-search="false"
            />
        </div>
    </div>
</x-layouts.dashboard>
