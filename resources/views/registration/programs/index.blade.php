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
    $selectedNextUrl = $selectedProgram['next_url'] ?? '/registration/form';
    $programOptions = $programs->mapWithKeys(fn ($program) => [$program['id'] => $program['name']])->all();
    $programHelpers = $programs->mapWithKeys(fn ($program) => [
        $program['id'] => $program['description'].' - Biaya pendaftaran '.$formatRupiah($program['registration_fee']),
    ])->all();
    $programIcons = $programs->mapWithKeys(fn ($program) => [
        $program['id'] => $iconMap[$program['icon']] ?? 'program-general',
    ])->all();
    $programInputAttributes = $programs->mapWithKeys(fn ($program) => [
        $program['id'] => [
            'data-program-radio' => true,
            'data-name' => $program['name'],
            'data-icon' => $program['icon'],
            'data-tone' => $program['tone'],
            'data-price' => $program['registration_fee'],
            'data-next-url' => $program['next_url'],
        ],
    ])->all();
    $programWrapperAttributes = $programs->mapWithKeys(function ($program) use ($selectedProgram): array {
        $isSelected = $selectedProgram && $selectedProgram['id'] === $program['id'];

        return [
            $program['id'] => [
                'class' => implode(' ', array_filter([
                    'registration-program-card group relative cursor-pointer rounded-card border bg-white p-5 shadow-soft transition hover:-translate-y-1 hover:border-etc-magenta/70 hover:shadow-panel',
                    $isSelected ? 'is-selected border-etc-magenta ring-2 ring-etc-magenta/15' : 'border-etc-outline-variant',
                ])),
            ],
        ];
    })->all();
    $programIconAttributes = $programs->mapWithKeys(fn ($program) => [
        $program['id'] => [
            'data-program-icon' => true,
            'class' => 'mb-2 bg-etc-surface-low',
        ],
    ])->all();
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
                        <div class="mt-8">
                            <x-ui.empty-state
                                heading="Belum ada program aktif"
                                description="Program yang tersedia akan muncul di sini setelah diaktifkan admin."
                                icon="heroicon-o-academic-cap"
                                contained
                            />
                        </div>
                    @else
                        <form class="mt-8" action="#" method="POST">
                            @csrf
                            <x-ui.radio-group
                                name="program_id"
                                :value="$selectedProgram['id'] ?? null"
                                :options="$programOptions"
                                :option-helpers="$programHelpers"
                                :option-icons="$programIcons"
                                :option-attributes="$programInputAttributes"
                                :option-wrapper-attributes="$programWrapperAttributes"
                                :option-icon-attributes="$programIconAttributes"
                                columns="sm:grid-cols-2"
                                required
                            />
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

                            <x-ui.button
                                :href="$selectedNextUrl"
                                data-registration-continue
                                size="xl"
                                class="mt-8 w-full !rounded-pill"
                                icon="heroicon-m-arrow-right"
                                icon-position="after"
                            >
                                Lanjut ke Data Pribadi
                            </x-ui.button>
                        </div>
                    @else
                        <p class="mt-6 text-sm leading-6 text-white/75">Pilih program aktif setelah tersedia untuk melihat ringkasannya.</p>
                    @endif
                </aside>
            </div>
        </div>
    </section>
</x-layouts.public>
