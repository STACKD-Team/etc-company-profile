<x-layouts.dashboard title="Detail Siswa" area="admin" active="students">
    <x-ui.resource-header
        :title="$student->full_name ?? $student->name"
        :subtitle="$student->email.' - '.($student->mobile_phone ?? 'No HP belum diisi')"
        :back-url="route('admin.student.index')"
    >
        <x-slot:status>
            <x-ui.badge :status="$student->is_active ? 'active' : 'inactive'">{{ $student->is_active ? 'Aktif' : 'Nonaktif' }}</x-ui.badge>
        </x-slot:status>
        <x-slot:actions>
            <x-ui.button :href="route('admin.student.edit', $student)" icon="heroicon-m-pencil-square">Edit</x-ui.button>
            <x-ui.delete-action :action="route('admin.student.destroy', $student)" heading="Hapus siswa?" />
        </x-slot:actions>
    </x-ui.resource-header>

    <div class="grid gap-4 md:grid-cols-3">
        <x-ui.stat-card label="Enrollment" :value="$student->enrollments->count()" icon="heroicon-m-academic-cap" />
        <x-ui.stat-card label="Rapor" :value="$student->enrollments->filter(fn ($enrollment) => $enrollment->reportCard)->count()" icon="heroicon-m-document-text" />
        <x-ui.stat-card label="Pendaftaran" :value="$student->registrations->count()" icon="heroicon-m-clipboard-document-list" />
    </div>

    <x-ui.detail-card heading="Profil Siswa" class="mt-6">
        <x-ui.description-list columns="4">
            <x-ui.description-item label="No Induk" :value="$student->no_induk ?: '-'" />
            <x-ui.description-item label="Jenis Kelamin" :value="$student->sex ?: '-'" />
            <x-ui.description-item label="TTL" :value="trim(($student->place_of_birth ?: '-').' / '.($student->date_of_birth?->format('d M Y') ?: '-'))" />
            <x-ui.description-item label="Sekolah/Pekerjaan" :value="$student->occupation_school ?: '-'" />
            <x-ui.description-item label="Alamat" :value="$student->address ?: '-'" />
            <x-ui.description-item label="Orang Tua" :value="trim(($student->father_name ?: '-').' / '.($student->mother_name ?: '-'))" />
            <x-ui.description-item label="NISN" :value="$student->nisn ?: '-'" />
            <x-ui.description-item label="NIK" :value="$student->nik ?: '-'" />
        </x-ui.description-list>
    </x-ui.detail-card>

    <x-ui.detail-card heading="Histori Kelas" description="Bersumber dari enrollments agar admin melihat perjalanan belajar lengkap." class="mt-6">
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
                'actions' => 'Aksi',
            ]"
            row-view="pages.admin.student.partials.enrollment-row"
            empty="Belum ada histori kelas"
            empty-description="Histori akan muncul setelah siswa dimasukkan ke kelas."
            :show-search="false"
        />
    </x-ui.detail-card>
</x-layouts.dashboard>
