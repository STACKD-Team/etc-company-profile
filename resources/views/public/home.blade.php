<x-layouts.public title="Beranda">
    @php
        $assetUrl = static fn (?string $path, string $fallback = 'images/hero-img.jpeg') => asset($path ?: $fallback);
        $formatMoney = static fn ($value) => 'Rp ' . number_format((float) $value, 0, ',', '.');
    @endphp

    <section class="relative overflow-hidden bg-[linear-gradient(108deg,#ffffff_0%,#ffffff_54%,#fdeaf0_54%,#fdeaf0_100%)]">
        <div class="mx-auto grid min-h-[760px] max-w-[1120px] items-center gap-12 px-5 py-20 lg:grid-cols-[1.02fr_0.98fr] lg:px-0">
            <div>
                <p class="font-heading text-sm font-black uppercase tracking-[0.18em] text-etc-magenta">ETC Planet Padang</p>
                <h1 class="mt-4 max-w-[560px] font-heading text-[44px] font-black leading-[1.18] tracking-normal text-[#2a1820] md:text-[56px] lg:text-[62px]">
                    Belajar Lebih Seru, Masa Depan Lebih Cerah
                </h1>
                <p class="mt-8 max-w-[520px] text-[17px] leading-8 text-[#6e5860]">
                    Lembaga kursus bahasa yang ramah untuk siswa, kredibel untuk orang tua, dan fokus membantu kamu percaya diri memakai bahasa di dunia nyata.
                </p>

                <div class="mt-10 flex flex-col gap-4 sm:flex-row">
                    <a href="{{ route('public.contact.index') }}" class="inline-flex min-h-12 items-center justify-center gap-2 rounded-full bg-etc-magenta px-8 py-3 font-heading text-[14px] font-bold text-white shadow-soft transition hover:bg-etc-primary">
                        Daftar Sekarang
                        <span class="material-symbols-outlined text-base">arrow_forward</span>
                    </a>
                    <a href="#program" class="inline-flex min-h-12 items-center justify-center rounded-full border-2 border-[#e7b8c8] bg-white/70 px-8 py-3 font-heading text-[14px] font-bold text-[#2a1820] transition hover:border-etc-magenta hover:text-etc-magenta">
                        Lihat Program
                    </a>
                </div>

                <div class="mt-10 h-px max-w-[520px] bg-[#eddde2]"></div>

                <div class="mt-7 flex flex-wrap gap-8 text-[13px] font-bold text-[#2a1820]">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-[22px] text-etc-magenta">groups</span>
                        <span>{{ $stats['students'] }} Siswa</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-[22px] text-etc-magenta">school</span>
                        <span>{{ $stats['instructors'] }} Instruktur</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-[22px] text-etc-magenta">article</span>
                        <span>{{ $stats['programs'] }} Program</span>
                    </div>
                </div>
            </div>

            <div class="relative mx-auto w-full max-w-[470px] lg:mx-0">
                <img src="{{ asset('images/hero-img.jpeg') }}" alt="Guru ETC Planet mengajar siswa di kelas" class="aspect-[0.86] w-full rounded-2xl border-4 border-white object-cover shadow-[0_12px_28px_rgba(58,44,51,0.16)]">
                <div class="absolute -bottom-7 -left-4 flex items-center gap-4 rounded-2xl border border-[#f3c8d7] bg-white/95 px-5 py-4 shadow-[0_16px_28px_rgba(58,44,51,0.18)] backdrop-blur md:-left-7">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-[#ffe0ed] text-etc-magenta">
                        <span class="material-symbols-outlined text-[24px]">verified</span>
                    </div>
                    <div class="leading-tight">
                        <p class="font-heading text-[14px] font-black text-[#2a1820]">Biaya Pendaftaran</p>
                        <p class="font-heading text-[13px] font-black text-etc-magenta">Mulai Rp 200.000</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-[#2b2b2b] py-12 text-white">
        <div class="mx-auto grid max-w-[1120px] grid-cols-2 gap-y-8 px-5 md:grid-cols-4 lg:px-0">
            @foreach ([
                ['value' => $stats['students'], 'label' => 'Siswa Terdata'],
                ['value' => $stats['instructors'], 'label' => 'Instruktur'],
                ['value' => $stats['programs'], 'label' => 'Program Aktif'],
                ['value' => $stats['satisfaction'], 'label' => 'Kepuasan Siswa'],
            ] as $index => $stat)
                <div @class(['text-center', 'md:border-l md:border-white/10' => $index > 0])>
                    <p class="font-heading text-[34px] font-black leading-none">{{ $stat['value'] }}</p>
                    <p class="mt-4 font-heading text-[11px] font-bold uppercase tracking-[0.2em] text-zinc-400">{{ $stat['label'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    <section id="program" class="bg-[#fff7f7] py-24">
        <div class="mx-auto max-w-[1120px] px-5 lg:px-0">
            <div class="mx-auto max-w-[650px] text-center">
                <h2 class="font-heading text-[36px] font-black leading-tight text-[#2a1820]">Program Kursus Unggulan</h2>
                <p class="mt-4 text-[15px] leading-7 text-[#765f67]">
                    Pilihan program akan mengikuti data program aktif. Jika belum tersedia, tim ETC tetap bisa membantu konsultasi program melalui kontak.
                </p>
            </div>

            @if ($programs->isNotEmpty())
                <div class="mt-16 grid gap-8 md:grid-cols-3">
                    @foreach ($programs as $program)
                        <article class="group overflow-hidden rounded-[22px] border border-[#eeb8c9] bg-white shadow-soft">
                            <div class="relative h-[190px] overflow-hidden bg-[#fff1f6]">
                                <span class="absolute left-5 top-5 z-10 rounded-full bg-etc-magenta px-4 py-2 font-heading text-[12px] font-black uppercase text-white">
                                    {{ str($program->category)->replace('_', ' ')->headline() }}
                                </span>
                                <img src="{{ $assetUrl($program->thumbnail, 'images/pu1-img.jpg') }}" alt="{{ $program->name }}" class="h-full w-full object-cover">
                            </div>
                            <div class="p-6">
                                <div class="flex items-center justify-between gap-3">
                                    <h3 class="font-heading text-[25px] font-black leading-tight text-[#2a1820]">{{ $program->name }}</h3>
                                    <span class="inline-flex shrink-0 rounded-md bg-[#ffe6f3] px-3 py-2 font-heading text-[12px] font-black text-[#2a1820]">
                                        {{ str($program->target_age ?? 'all')->headline() }}
                                    </span>
                                </div>
                                <p class="mt-4 min-h-[72px] text-[15px] leading-7 text-[#765f67]">{{ $program->description ?: 'Program aktif ETC Planet dengan kelas kecil dan pendampingan instruktur.' }}</p>
                                <div class="mt-7 border-t border-[#f0dde3] pt-5">
                                    <div class="flex items-end justify-between">
                                        <div>
                                            <p class="font-heading text-[11px] font-bold uppercase tracking-[0.18em] text-[#765f67]">Mulai Dari</p>
                                            <p class="mt-2 font-heading text-[22px] font-black text-etc-magenta">{{ $formatMoney($program->price) }}</p>
                                        </div>
                                        <a href="{{ route('public.contact.index') }}" class="text-etc-magenta" aria-label="Konsultasi {{ $program->name }}">
                                            <span class="material-symbols-outlined text-[28px]">arrow_forward</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="mx-auto mt-14 max-w-2xl rounded-[22px] border border-dashed border-[#eeb8c9] bg-white p-8 text-center shadow-soft">
                    <span class="material-symbols-outlined text-4xl text-etc-magenta">school</span>
                    <h3 class="mt-4 font-heading text-2xl font-black text-[#2a1820]">Data program sedang disiapkan</h3>
                    <p class="mt-3 text-[#765f67]">Kamu tetap bisa konsultasi program yang cocok melalui form kontak ETC Planet.</p>
                </div>
            @endif
        </div>
    </section>

    <section class="bg-[#fff0f3] py-20">
        <div class="mx-auto max-w-[1120px] px-5 lg:px-0">
            <h2 class="text-center font-heading text-[36px] font-black text-[#2a1820]">Alur Pendaftaran Mudah</h2>
            <div class="relative mt-14 grid gap-10 md:grid-cols-5">
                <div class="absolute left-[8%] right-[8%] top-[44px] hidden h-1 bg-[#f0c9d4] md:block"></div>
                @foreach ([
                    ['title' => 'Pilih Program', 'desc' => 'Tentukan target belajar bersama tim ETC.', 'icon' => 'touch_app'],
                    ['title' => 'Isi Formulir', 'desc' => 'Lengkapi data saat flow registration siap.', 'icon' => 'apps'],
                    ['title' => 'Pembayaran', 'desc' => 'Selesaikan biaya awal sesuai instruksi.', 'icon' => 'payments'],
                    ['title' => 'Placement Test', 'desc' => 'Ikuti tes penempatan offline.', 'icon' => 'assignment'],
                    ['title' => 'Mulai Belajar', 'desc' => 'Masuk kelas sesuai level.', 'icon' => 'school'],
                ] as $step)
                    <div class="relative z-10 text-center">
                        <div class="mx-auto flex h-[62px] w-[62px] items-center justify-center rounded-full border-4 border-etc-magenta bg-etc-magenta font-bold text-white shadow-soft">
                            <span class="material-symbols-outlined text-[30px]">{{ $step['icon'] }}</span>
                        </div>
                        <h3 class="mt-5 font-heading text-[19px] font-bold text-[#2a1820]">{{ $step['title'] }}</h3>
                        <p class="mt-3 text-[14px] leading-6 text-[#765f67]">{{ $step['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section id="reels" class="bg-[#2b2b2b] py-24 text-white">
        <div class="mx-auto max-w-[1120px] px-5 lg:px-0">
            <div class="flex flex-col justify-between gap-6 md:flex-row md:items-end">
                <div>
                    <h2 class="font-heading text-[36px] font-black">Galeri Keseruan</h2>
                    <p class="mt-3 text-[16px] text-zinc-400">Intip suasana belajar dari reels public ETC Planet.</p>
                </div>
                <a href="{{ route('public.reels.index') }}" class="inline-flex items-center gap-1 font-heading text-[13px] font-black uppercase tracking-wide text-white transition hover:text-etc-magenta">
                    Lihat Semua
                    <span class="material-symbols-outlined text-[24px]">arrow_forward</span>
                </a>
            </div>

            @if ($reels->isNotEmpty())
                <div class="mt-12 grid gap-7 md:grid-cols-4">
                    @foreach ($reels as $reel)
                        <a href="{{ route('public.reels.show', $reel) }}" class="group overflow-hidden rounded-2xl border border-white/10 bg-[#1f1f1f] text-left shadow-[0_18px_34px_rgba(0,0,0,0.28)]">
                            <div class="relative aspect-[9/13] overflow-hidden bg-black">
                                <video preload="metadata" muted playsinline poster="{{ $assetUrl($reel->thumbnail_path, 'images/pu1-img (3).jpg') }}" class="h-full w-full object-cover opacity-90">
                                    <source src="{{ $assetUrl($reel->video_path, 'videos/video1.mp4') }}" type="video/mp4">
                                </video>
                                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/10 to-black/5"></div>
                                <span class="absolute left-4 top-4 rounded-full bg-etc-magenta px-3 py-1 font-heading text-[11px] font-black uppercase tracking-wide text-white">
                                    {{ $reel->category }}
                                </span>
                                <span class="absolute left-1/2 top-1/2 flex h-14 w-14 -translate-x-1/2 -translate-y-1/2 items-center justify-center rounded-full bg-white/90 text-etc-magenta opacity-0 shadow-soft transition group-hover:opacity-100">
                                    <span class="material-symbols-outlined text-[34px]">play_arrow</span>
                                </span>
                            </div>
                            <div class="min-h-[118px] border-t border-white/10 p-5">
                                <h3 class="font-heading text-[15px] font-black leading-6 text-white">{{ $reel->title }}</h3>
                                <div class="mt-4 flex items-center justify-between text-[12px] text-zinc-400">
                                    <p class="flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[15px]">visibility</span>
                                        {{ number_format((int) $reel->views_count) }}
                                    </p>
                                    <p class="flex items-center gap-1 text-etc-magenta">
                                        <span class="material-symbols-outlined text-[16px]">favorite</span>
                                        {{ number_format((int) $reel->likes_count) }}
                                    </p>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="mt-12 rounded-2xl border border-white/10 bg-white/5 p-8 text-center text-zinc-300">
                    Reels published belum tersedia.
                </div>
            @endif
        </div>
    </section>

    <section class="bg-[#fff0f3] py-24">
        <div class="mx-auto max-w-[1120px] px-5 text-center lg:px-0">
            <h2 class="font-heading text-[36px] font-black text-[#2a1820]">Pengajar Profesional</h2>
            <p class="mt-4 text-[16px] text-[#765f67]">Belajar bersama instruktur yang ditampilkan khusus di halaman team ETC Planet.</p>

            @if ($instructors->isNotEmpty())
                <div class="mt-16 grid gap-10 md:grid-cols-4">
                    @foreach ($instructors as $teacher)
                        <article>
                            <img src="{{ $assetUrl($teacher->avatar, 'images/Ms. Debby.jpeg') }}" alt="Foto {{ $teacher->name }}" class="mx-auto h-[148px] w-[148px] rounded-full border-4 border-white object-cover shadow-soft">
                            <h3 class="mt-7 font-heading text-[21px] font-black text-[#2a1820]">{{ $teacher->name }}</h3>
                            <p class="mt-2 font-heading text-[14px] font-bold text-etc-magenta">{{ $teacher->instructor_position }}</p>
                            <p class="mt-2 text-[13px] text-[#765f67]">{{ $teacher->instructor_specialization }}</p>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="mx-auto mt-12 max-w-xl rounded-2xl border border-dashed border-[#eeb8c9] bg-white p-8 text-[#765f67] shadow-soft">
                    Data pengajar yang tampil di halaman team belum tersedia.
                </div>
            @endif
        </div>
    </section>
</x-layouts.public>
