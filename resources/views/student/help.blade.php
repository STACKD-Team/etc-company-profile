<x-layouts.dashboard title="Bantuan" area="student" active="help" :user="$student">
    <section class="rounded-card bg-white p-6 shadow-panel">
        <h2 class="font-heading text-2xl font-black">Butuh bantuan?</h2>
        <p class="mt-3 max-w-2xl text-sm leading-6 text-etc-on-muted">Hubungi admin ETC Planet untuk bantuan jadwal, kelas, rapor, atau kendala akun siswa.</p>
        <div class="mt-6 grid gap-4 md:grid-cols-3">
            <div class="rounded-card bg-etc-surface-low p-4"><strong>Akademik</strong><p class="mt-2 text-sm text-etc-on-muted">Jadwal, kelas, dan instruktur.</p></div>
            <div class="rounded-card bg-etc-surface-low p-4"><strong>Rapor</strong><p class="mt-2 text-sm text-etc-on-muted">Publikasi dan unduhan rapor.</p></div>
            <div class="rounded-card bg-etc-surface-low p-4"><strong>Akun</strong><p class="mt-2 text-sm text-etc-on-muted">Profil dan akses dashboard.</p></div>
        </div>
    </section>
</x-layouts.dashboard>
