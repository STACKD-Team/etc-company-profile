@php
    $footerLinkGroups = [
        'ETC Planet' => [
            ['label' => 'Beranda', 'url' => '#'],
            ['label' => 'Program', 'route' => 'registrations.programs.index'],
            ['label' => 'Tentang Kami', 'url' => '#'],
        ],
        'Program' => $programs->take(4)->map(fn ($program) => ['label' => $program['name'], 'url' => '#program'])->all(),
    ];

    $iconMap = [
        'general-english' => 'program-general',
        'toefl-preparation' => 'program-toefl',
        'bahasa-asia' => 'program-asia',
        'kids-english' => 'program-kids',
    ];

    $formatRupiah = static fn (int $value): string => 'Rp '.number_format($value, 0, ',', '.');
    $selectedProgram ??= $programs->first();
    $selectedIcon = $selectedProgram ? ($iconMap[$selectedProgram['icon']] ?? 'program-general') : 'program-general';
    $selectedContactUrl = $selectedProgram['contact_url'] ?? route('public.contact.index');
@endphp

<x-layouts.public title="Pilih Program" :footer-link-groups="$footerLinkGroups">
    <section class="bg-etc-surface py-10 md:py-14" data-registration-program-page>
        <div class="mx-auto max-w-[1200px] px-6 lg:px-8">
            <ol class="grid grid-cols-2 gap-3 rounded-card bg-white p-3 shadow-soft md:grid-cols-4" aria-label="Tahapan pendaftaran">
                @foreach (['Pilih Program', 'Data Pribadi', 'Pembayaran', 'Konfirmasi'] as $index => $step)
                    <li
                        @class([
                            'stepper-item flex items-center gap-3 rounded-xl px-4 py-3 font-heading text-sm font-semibold transition',
                            'is-active bg-etc-magenta text-white' => $index === 0,
                            'bg-etc-surface-low text-etc-on-muted' => $index !== 0,
                        ])
                    >
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-white/80 text-sm font-bold text-etc-magenta">{{ $index + 1 }}</span>
                        <span>{{ $step }}</span>
                    </li>
                @endforeach
            </ol>

            <div class="mt-8 grid gap-8 lg:grid-cols-[minmax(0,1fr)_360px]">
                <section class="rounded-card bg-white p-6 shadow-panel md:p-8" aria-labelledby="page-title">
                    <p class="font-heading text-xs font-bold uppercase text-etc-magenta">Langkah 1 dari 4</p>
                    <h1 id="page-title" class="mt-3 font-heading text-3xl font-black text-etc-on-surface md:text-4xl">Pilih Program Belajar</h1>
                    <p class="mt-3 max-w-2xl text-sm leading-6 text-etc-on-muted md:text-base">
                        Pilih program yang paling sesuai dengan tujuan belajarmu bersama ETC Planet.
                    </p>

                    @if ($programs->isEmpty())
                        <div class="mt-8 rounded-card border border-dashed border-etc-outline-variant bg-etc-surface-low p-8 text-center">
                            <p class="font-heading text-lg font-bold text-etc-on-surface">Belum ada program aktif.</p>
                            <p class="mt-2 text-sm text-etc-on-muted">Program yang tersedia akan muncul di sini setelah diaktifkan admin.</p>
                        </div>
                    @else
                        <form class="mt-8" action="#" method="POST">
                            @csrf
                            <div class="grid gap-4 sm:grid-cols-2">
                                @foreach ($programs as $program)
                                    @php
                                        $programIcon = $iconMap[$program['icon']] ?? 'program-general';
                                        $isSelected = $selectedProgram && $selectedProgram['id'] === $program['id'];
                                    @endphp
                                    <label
                                        @class([
                                            'registration-program-card group relative cursor-pointer rounded-card border bg-white p-5 shadow-soft transition hover:-translate-y-1 hover:border-etc-magenta/70 hover:shadow-panel',
                                            'is-selected border-etc-magenta ring-2 ring-etc-magenta/15' => $isSelected,
                                            'border-etc-outline-variant' => ! $isSelected,
                                        ])
                                    >
                                        <input
                                            class="sr-only"
                                            type="radio"
                                            name="program_id"
                                            value="{{ $program['id'] }}"
                                            data-program-radio
                                            data-name="{{ $program['name'] }}"
                                            data-icon="{{ $program['icon'] }}"
                                            data-tone="{{ $program['tone'] }}"
                                            data-price="{{ $program['registration_fee'] }}"
                                            data-contact-url="{{ $program['contact_url'] }}"
                                            @checked($isSelected)
                                        >
                                        <span class="absolute right-4 top-4 flex h-7 w-7 items-center justify-center rounded-full border border-etc-outline-variant bg-white text-etc-magenta transition group-has-[:checked]:border-etc-magenta group-has-[:checked]:bg-etc-magenta group-has-[:checked]:text-white">
                                            <span class="material-symbols-outlined text-base">check</span>
                                        </span>
                                        <span class="mb-5 flex h-14 w-14 items-center justify-center rounded-2xl bg-etc-surface-low" data-program-icon>
                                            <x-ui.icon :name="$programIcon" class="h-7 w-7" />
                                        </span>
                                        <span class="block font-heading text-lg font-bold text-etc-on-surface">{{ $program['name'] }}</span>
                                        <span class="mt-2 block min-h-12 text-sm leading-6 text-etc-on-muted">{{ $program['description'] }}</span>
                                        <span class="mt-4 block font-heading text-sm font-bold text-etc-magenta">{{ $formatRupiah($program['registration_fee']) }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </form>
                    @endif
                </section>

                <aside class="registration-summary h-fit rounded-card bg-etc-charcoal p-6 text-white shadow-panel" aria-label="Ringkasan pendaftaran">
                    <h2 class="font-heading text-xl font-bold">Ringkasan Pendaftaran</h2>

                    @if ($selectedProgram)
                        <div class="mt-8">
                            <p class="text-xs font-bold uppercase text-white/60">Program Terpilih</p>
                            <div class="mt-4 flex items-center gap-4">
                                <span class="flex h-14 w-14 items-center justify-center rounded-2xl bg-white" id="summary-icon-wrap" data-summary-icon>
                                    <x-ui.icon :name="$selectedIcon" class="h-7 w-7" />
                                </span>
                                <strong id="summary-name" class="font-heading text-lg">{{ $selectedProgram['name'] }}</strong>
                            </div>

                            <div class="my-6 h-px bg-white/10"></div>

                            <div class="flex items-center justify-between gap-4 text-sm">
                                <span class="text-white/75">Biaya Pendaftaran</span>
                                <strong id="summary-price" class="font-heading">{{ $formatRupiah($selectedProgram['registration_fee']) }}</strong>
                            </div>
                            <p class="mt-2 text-xs text-white/60">Biaya program akan diinformasikan pada langkah berikutnya.</p>

                            <div class="my-6 h-px bg-white/10"></div>

                            <div class="flex items-center justify-between gap-4">
                                <span class="text-sm text-white/75">Total Sementara</span>
                                <strong id="summary-total" class="font-heading text-xl text-etc-magenta">{{ $formatRupiah($selectedProgram['registration_fee']) }}</strong>
                            </div>

                            <a href="{{ $selectedContactUrl }}" data-registration-continue class="mt-8 inline-flex min-h-12 w-full items-center justify-center gap-2 rounded-pill bg-etc-magenta px-5 py-3 font-heading text-sm font-bold text-white transition hover:bg-etc-primary">
                                Lanjut ke Data Pribadi
                                <span class="material-symbols-outlined text-lg">arrow_forward</span>
                            </a>
                        </div>
                    @else
                        <p class="mt-6 text-sm leading-6 text-white/75">Pilih program aktif setelah tersedia untuk melihat ringkasannya.</p>
                    @endif
                </aside>
            </div>
        </div>
    </section>
</x-layouts.public>
