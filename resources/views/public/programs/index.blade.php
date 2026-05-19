@php
    $navbarItems = [
        ['label' => 'Beranda', 'url' => '#', 'key' => 'home', 'icon' => 'home'],
        ['label' => 'Program', 'route' => 'public.programs.index', 'key' => 'program', 'icon' => 'school'],
        ['label' => 'Tentang Kami', 'url' => '#', 'key' => 'about', 'icon' => 'groups'],
        ['label' => 'Testimoni', 'url' => '#', 'key' => 'testimonials', 'icon' => 'reviews'],
    ];

    $categoryLabels = [
        'english' => 'English',
        'mandarin' => 'Mandarin',
        'japanese' => 'Japanese',
        'test_prep' => 'Test Prep',
        'soft_skills' => 'Soft Skills',
        'other' => 'Lainnya',
    ];

    $typeLabels = [
        'regular' => 'Regular',
        'private' => 'Private',
        'one_on_one' => 'One on One',
    ];

    $targetAgeLabels = [
        'kids' => 'Kids',
        'teen' => 'Teen',
        'adult' => 'Adult',
        'university' => 'University',
        'all' => 'Semua Usia',
    ];

    $formatLabel = static fn (?string $value, array $labels): string => $labels[$value] ?? str($value ?: '-')->replace('_', ' ')->headline()->toString();
    $formatRupiah = static fn ($value): string => 'Rp '.number_format((int) $value, 0, ',', '.');
@endphp

<x-layouts.public title="Program" :navbar-items="$navbarItems">
    <section class="bg-etc-surface py-12 md:py-16">
        <div class="mx-auto max-w-[1200px] px-6 lg:px-8">
            <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                <div>
                    <p class="font-heading text-xs font-bold uppercase text-etc-magenta">Program ETC Planet</p>
                    <h1 class="mt-3 font-heading text-4xl font-black text-etc-on-surface">Pilih jalur belajar yang cocok</h1>
                    <p class="mt-3 max-w-2xl text-sm leading-6 text-etc-on-muted">Bandingkan program aktif ETC Planet berdasarkan kategori, tipe kelas, durasi, dan biaya sebelum mulai pendaftaran.</p>
                </div>
                <a href="{{ route('registrations.programs.index') }}" class="inline-flex min-h-12 items-center justify-center rounded-pill bg-etc-magenta px-5 py-3 font-heading text-sm font-bold text-white hover:bg-etc-primary">
                    Mulai Pendaftaran
                </a>
            </div>

            <nav aria-label="Filter kategori program" class="mt-8 flex flex-wrap gap-3">
                <a href="{{ route('public.programs.index') }}" @class(['inline-flex min-h-10 items-center rounded-full px-4 py-2 font-heading text-sm font-bold shadow-soft transition hover:-translate-y-0.5', 'bg-etc-magenta text-white' => $selectedCategory === '', 'bg-white text-etc-on-muted hover:text-etc-magenta' => $selectedCategory !== ''])>Semua</a>
                @foreach ($categories as $category)
                    <a href="{{ route('public.programs.index', ['category' => $category]) }}" @class(['inline-flex min-h-10 items-center rounded-full px-4 py-2 font-heading text-sm font-bold shadow-soft transition hover:-translate-y-0.5', 'bg-etc-magenta text-white' => $selectedCategory === $category, 'bg-white text-etc-on-muted hover:text-etc-magenta' => $selectedCategory !== $category])>
                        {{ $formatLabel($category, $categoryLabels) }}
                    </a>
                @endforeach
            </nav>

            <div class="mt-8 grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                @forelse ($programs as $program)
                    @php
                        $thumbnail = $program->thumbnail;
                        $thumbnailUrl = $thumbnail
                            ? (\Illuminate\Support\Str::startsWith($thumbnail, ['http://', 'https://', '/'])
                                ? $thumbnail
                                : \Illuminate\Support\Facades\Storage::url($thumbnail))
                            : null;
                    @endphp

                    <article class="group flex h-full flex-col overflow-hidden rounded-card bg-white shadow-soft ring-1 ring-etc-outline-variant/60 transition duration-200 hover:-translate-y-1 hover:shadow-panel">
                        <div class="relative aspect-[16/10] overflow-hidden bg-etc-charcoal">
                            @if ($thumbnailUrl)
                                <img src="{{ $thumbnailUrl }}" alt="{{ $program->name }}" class="h-full w-full object-cover transition duration-300 group-hover:scale-105">
                                <div class="absolute inset-0 bg-gradient-to-t from-etc-charcoal/65 via-transparent to-transparent"></div>
                            @else
                                <div class="flex h-full w-full items-center justify-center bg-[radial-gradient(circle_at_20%_20%,#ffe1e8_0,#ffe1e8_28%,transparent_29%),linear-gradient(135deg,#3a2c33_0%,#5a3f47_48%,#e6007f_100%)]">
                                    <div class="rounded-3xl bg-white/12 px-5 py-4 text-center backdrop-blur-sm">
                                        <span class="material-symbols-outlined text-4xl text-white">school</span>
                                        <p class="mt-2 font-heading text-sm font-black uppercase tracking-wide text-white">ETC Planet</p>
                                    </div>
                                </div>
                            @endif

                            <span class="absolute left-4 top-4 rounded-full bg-white px-3 py-1 font-heading text-xs font-bold text-etc-magenta shadow-soft">
                                {{ $formatLabel($program->category, $categoryLabels) }}
                            </span>
                        </div>

                        <div class="flex flex-1 flex-col p-6">
                            <h2 class="font-heading text-xl font-bold text-etc-on-surface">{{ $program->name }}</h2>
                            <p class="mt-3 min-h-20 text-sm leading-6 text-etc-on-muted">{{ $program->description ?: 'Program belajar ETC Planet yang dirancang untuk kebutuhan siswa.' }}</p>

                            <dl class="mt-5 grid grid-cols-2 gap-3 text-sm">
                                <div class="rounded-2xl bg-etc-surface-low p-3">
                                    <dt class="text-xs font-bold uppercase text-etc-on-muted">Durasi</dt>
                                    <dd class="mt-1 font-heading font-bold text-etc-on-surface">{{ $program->duration_meetings ?? 0 }} pertemuan</dd>
                                </div>
                                <div class="rounded-2xl bg-etc-surface-low p-3">
                                    <dt class="text-xs font-bold uppercase text-etc-on-muted">Target</dt>
                                    <dd class="mt-1 font-heading font-bold text-etc-on-surface">{{ $formatLabel($program->target_age, $targetAgeLabels) }}</dd>
                                </div>
                                <div class="rounded-2xl bg-etc-surface-low p-3">
                                    <dt class="text-xs font-bold uppercase text-etc-on-muted">Tipe</dt>
                                    <dd class="mt-1 font-heading font-bold text-etc-on-surface">{{ $formatLabel($program->type, $typeLabels) }}</dd>
                                </div>
                                <div class="rounded-2xl bg-etc-surface-low p-3">
                                    <dt class="text-xs font-bold uppercase text-etc-on-muted">Kapasitas</dt>
                                    <dd class="mt-1 font-heading font-bold text-etc-on-surface">Maks. {{ $program->max_students ?? 0 }} siswa</dd>
                                </div>
                            </dl>

                            <div class="mt-6 rounded-2xl border border-etc-outline-variant bg-white p-4">
                                <div class="flex items-center justify-between gap-4">
                                    <span class="text-sm font-bold text-etc-on-muted">Harga program</span>
                                    <span class="font-heading text-lg font-black text-etc-magenta">{{ $formatRupiah($program->price) }}</span>
                                </div>
                                <div class="mt-2 flex items-center justify-between gap-4 text-sm">
                                    <span class="text-etc-on-muted">Biaya pendaftaran</span>
                                    <span class="font-bold text-etc-on-surface">{{ $formatRupiah($program->registration_fee) }}</span>
                                </div>
                            </div>

                            <a href="{{ route('registrations.programs.index') }}" class="mt-6 inline-flex min-h-12 items-center justify-center gap-2 rounded-pill bg-etc-magenta px-5 py-3 font-heading text-sm font-bold text-white transition hover:bg-etc-primary">
                                Daftar Program
                                <span class="material-symbols-outlined text-lg">arrow_forward</span>
                            </a>
                        </div>
                    </article>
                @empty
                    <div class="rounded-card bg-white p-8 text-center shadow-soft md:col-span-2 xl:col-span-3">
                        <p class="font-heading text-lg font-bold text-etc-on-surface">Belum ada program aktif.</p>
                        <p class="mt-2 text-sm text-etc-on-muted">Program yang tersedia akan tampil di sini setelah diaktifkan admin.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>
</x-layouts.public>
