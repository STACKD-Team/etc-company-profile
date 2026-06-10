<x-layouts.dashboard title="Bantuan" area="student" active="help" :user="$student">
    <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_360px]">
        <div class="space-y-6">
            <x-ui.panel heading="Pusat Bantuan Siswa" description="Pilih topik bantuan yang paling sesuai dengan kendala siswa atau orang tua.">
                <div class="grid gap-4 md:grid-cols-3">
                    <div class="rounded-card bg-etc-surface-low p-5">
                        <span class="material-symbols-outlined text-2xl text-etc-magenta">school</span>
                        <h3 class="mt-4 font-heading text-base font-bold text-etc-on-surface">Akademik</h3>
                        <p class="mt-2 text-sm leading-6 text-etc-on-muted">Jadwal, kelas aktif, instructor, dan riwayat belajar.</p>
                    </div>
                    <div class="rounded-card bg-etc-surface-low p-5">
                        <span class="material-symbols-outlined text-2xl text-etc-magenta">payments</span>
                        <h3 class="mt-4 font-heading text-base font-bold text-etc-on-surface">Pembayaran</h3>
                        <p class="mt-2 text-sm leading-6 text-etc-on-muted">Status pembayaran, nominal akhir, dan proses konfirmasi.</p>
                    </div>
                    <div class="rounded-card bg-etc-surface-low p-5">
                        <span class="material-symbols-outlined text-2xl text-etc-magenta">description</span>
                        <h3 class="mt-4 font-heading text-base font-bold text-etc-on-surface">Rapor</h3>
                        <p class="mt-2 text-sm leading-6 text-etc-on-muted">Publikasi rapor, download, nilai, dan komentar instructor.</p>
                    </div>
                </div>
            </x-ui.panel>

            <x-ui.panel heading="Pertanyaan Umum" description="Jawaban singkat untuk hal yang paling sering ditanyakan siswa.">
                <div class="divide-y divide-etc-outline-variant/60">
                    <details class="group py-4" open>
                        <summary class="cursor-pointer font-heading text-sm font-bold text-etc-on-surface">Kenapa rapor saya belum muncul?</summary>
                        <p class="mt-2 text-sm leading-6 text-etc-on-muted">Rapor hanya tampil setelah admin mempublish rapor. Jika kelas sudah selesai tetapi rapor belum muncul, hubungi admin akademik.</p>
                    </details>
                    <details class="group py-4">
                        <summary class="cursor-pointer font-heading text-sm font-bold text-etc-on-surface">Bagaimana melihat status pembayaran?</summary>
                        <p class="mt-2 text-sm leading-6 text-etc-on-muted">Buka menu Riwayat Pembayaran. Status terbaru, metode, nominal asli, potongan promo, dan nominal akhir akan tampil di sana.</p>
                    </details>
                    <details class="group py-4">
                        <summary class="cursor-pointer font-heading text-sm font-bold text-etc-on-surface">Bagaimana mengecek kelas yang pernah diikuti?</summary>
                        <p class="mt-2 text-sm leading-6 text-etc-on-muted">Buka Riwayat Pembelajaran dari dashboard atau menu Kelas Saya. Semua kelas aktif, selesai, dan berhenti akan ditampilkan.</p>
                    </details>
                </div>
            </x-ui.panel>
        </div>

        <aside class="space-y-6">
            <x-ui.panel heading="Chat Bantuan" description="Tampilan awal chatbot siswa. Integrasi RAG akan ditambahkan pada sprint integrasi.">
                <div class="space-y-3">
                    <div class="rounded-card bg-etc-charcoal p-4 text-white">
                        <p class="font-heading text-sm font-bold">ETC Assistant</p>
                        <p class="mt-2 text-sm leading-6 text-white/75">Halo, {{ $student->full_name ?? $student->name }}. Saya bisa membantu mengarahkan pertanyaan tentang kelas, pembayaran, dan rapor.</p>
                    </div>
                    <div class="ml-8 rounded-card bg-etc-surface-low p-4">
                        <p class="text-sm leading-6 text-etc-on-surface">Pilih kategori di halaman ini, lalu hubungi admin jika butuh tindak lanjut.</p>
                    </div>
                </div>
            </x-ui.panel>

            <x-ui.panel heading="Kontak Admin" description="Gunakan kanal resmi untuk bantuan yang perlu dicek manual.">
                <div class="space-y-3 text-sm">
                    <div class="rounded-card bg-etc-surface-low p-4">
                        <p class="font-heading font-bold text-etc-on-surface">Akademik dan kelas</p>
                        <p class="mt-1 text-etc-on-muted">Minta bantuan jadwal, kelas aktif, dan riwayat pembelajaran.</p>
                    </div>
                    <div class="rounded-card bg-etc-surface-low p-4">
                        <p class="font-heading font-bold text-etc-on-surface">Pembayaran dan rapor</p>
                        <p class="mt-1 text-etc-on-muted">Minta pengecekan status pembayaran atau publish rapor.</p>
                    </div>
                </div>
            </x-ui.panel>
        </aside>
    </div>
</x-layouts.dashboard>
