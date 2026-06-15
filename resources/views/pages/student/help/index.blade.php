<x-layouts.dashboard title="Bantuan" area="student" active="help" :user="$student">
    <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_360px]">
        <div class="space-y-6">
            <x-ui.panel heading="Pusat Bantuan Siswa" description="Pilih topik bantuan yang paling sesuai dengan kendala siswa atau orang tua.">
                <div class="grid gap-4 md:grid-cols-3">
                    <article class="student-reveal rounded-box border-2 border-etc-outline-variant bg-etc-surface p-5 shadow-soft" data-reveal-card>
                        <span class="material-symbols-outlined text-2xl text-etc-magenta">school</span>
                        <h3 class="mt-4 font-heading text-base font-bold text-etc-on-surface">Akademik</h3>
                        <p class="mt-2 text-sm leading-6 text-etc-on-muted">Jadwal, kelas aktif, instructor, dan riwayat belajar.</p>
                    </article>
                    <article class="student-reveal rounded-box border-2 border-etc-outline-variant bg-etc-surface p-5 shadow-soft" data-reveal-card>
                        <span class="material-symbols-outlined text-2xl text-etc-magenta">payments</span>
                        <h3 class="mt-4 font-heading text-base font-bold text-etc-on-surface">Pembayaran</h3>
                        <p class="mt-2 text-sm leading-6 text-etc-on-muted">Status pembayaran, nominal akhir, dan proses konfirmasi.</p>
                    </article>
                    <article class="student-reveal rounded-box border-2 border-etc-outline-variant bg-etc-surface p-5 shadow-soft" data-reveal-card>
                        <span class="material-symbols-outlined text-2xl text-etc-magenta">description</span>
                        <h3 class="mt-4 font-heading text-base font-bold text-etc-on-surface">Rapor</h3>
                        <p class="mt-2 text-sm leading-6 text-etc-on-muted">Publikasi rapor, download, nilai, dan komentar instructor.</p>
                    </article>
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
            <x-ui.panel heading="Chat Bantuan" description="Tanyakan kelas, pembayaran, riwayat belajar, dan rapor dari portal siswa.">
                <div
                    class="space-y-4"
                    data-chatbot-widget
                    data-chatbot-endpoint="{{ route('public.chatbot.messages.store') }}"
                    aria-label="Chat bantuan siswa ETC Planet"
                >
                    <div class="overflow-hidden rounded-box border-2 border-etc-outline-variant bg-etc-surface shadow-soft" data-chatbot-panel>
                        <div class="bg-etc-charcoal p-4 text-etc-surface">
                            <p class="font-heading text-sm font-bold">ETC Assistant</p>
                            <p class="mt-2 text-sm leading-6 text-etc-surface/75">Halo, {{ $student->full_name ?? $student->name }}. Saya bisa bantu menjawab pertanyaan tentang status belajar, pembayaran, dan rapor.</p>
                        </div>

                        <div class="max-h-96 space-y-3 overflow-y-auto bg-etc-surface-low p-4" data-chatbot-messages>
                            <p class="max-w-[90%] rounded-card bg-etc-surface px-4 py-3 text-sm leading-6 text-etc-on-muted shadow-soft">
                                Coba tanyakan: "Bagaimana cek rapor saya?" atau "Di mana lihat status pembayaran?"
                            </p>
                        </div>

                        <div class="border-t-2 border-etc-outline-variant bg-etc-surface p-4">
                            <div class="mb-3 flex flex-wrap gap-2" data-chatbot-suggestions>
                                @foreach ([
                                    'Bagaimana melihat kelas aktif saya?',
                                    'Di mana cek status pembayaran?',
                                    'Bagaimana download rapor?',
                                    'Apa isi riwayat belajar?',
                                ] as $suggestion)
                                    <x-ui.button
                                        type="button"
                                        color="gray"
                                        outlined
                                        size="xs"
                                        class="!rounded-pill"
                                        data-chatbot-suggestion="{{ $suggestion }}"
                                    >
                                        {{ $suggestion }}
                                    </x-ui.button>
                                @endforeach
                            </div>

                            <form class="flex gap-2" data-chatbot-form>
                                <x-ui.field
                                    id="student-help-chatbot-message"
                                    name="message"
                                    label="Pesan chatbot"
                                    label-class="sr-only"
                                    wrapper-class="min-w-0 flex-1"
                                    maxlength="1000"
                                    required
                                    autocomplete="off"
                                    placeholder="Tulis pertanyaan..."
                                />
                                <x-ui.icon-button
                                    type="submit"
                                    icon="heroicon-m-paper-airplane"
                                    label="Kirim pesan"
                                    color="primary"
                                    class="!rounded-pill"
                                    data-chatbot-submit
                                />
                            </form>
                        </div>
                    </div>
                </div>
            </x-ui.panel>

            <x-ui.panel heading="Kontak Admin" description="Gunakan kanal resmi untuk bantuan yang perlu dicek manual.">
                <div class="space-y-3 text-sm">
                    <div class="rounded-box bg-etc-surface-container p-4">
                        <p class="font-heading font-bold text-etc-on-surface">Akademik dan kelas</p>
                        <p class="mt-1 text-etc-on-muted">Minta bantuan jadwal, kelas aktif, dan riwayat pembelajaran.</p>
                    </div>
                    <div class="rounded-box bg-etc-surface-container p-4">
                        <p class="font-heading font-bold text-etc-on-surface">Pembayaran dan rapor</p>
                        <p class="mt-1 text-etc-on-muted">Minta pengecekan status pembayaran atau publish rapor.</p>
                    </div>
                </div>
            </x-ui.panel>
        </aside>
    </div>
</x-layouts.dashboard>
