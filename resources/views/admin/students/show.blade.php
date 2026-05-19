<x-layouts.dashboard title="Detail Siswa" area="admin" active="students">
    <section class="rounded-card bg-white p-6 shadow-panel">
        <h2 class="font-heading text-2xl font-black">{{ $student->full_name ?? $student->name }}</h2>
        <p class="mt-2 text-sm text-etc-on-muted">{{ $student->email }} • {{ $student->mobile_phone ?? '-' }}</p>
        <div class="mt-6 grid gap-4 md:grid-cols-2">
            <div class="rounded-card bg-etc-surface-low p-4"><strong>Alamat</strong><p class="mt-2 text-sm">{{ $student->address ?? '-' }}</p></div>
            <div class="rounded-card bg-etc-surface-low p-4"><strong>Sekolah/Pekerjaan</strong><p class="mt-2 text-sm">{{ $student->occupation_school ?? '-' }}</p></div>
        </div>
    </section>
</x-layouts.dashboard>
