<x-layouts.public title="Team Pengajar">
    @php($assetUrl = static fn (?string $path) => asset($path ?: 'images/Ms. Debby.jpeg'))

    <section class="bg-[#fff8f8] py-20">
        <div class="mx-auto max-w-[1120px] px-5 text-center lg:px-0">
            <p class="font-heading text-sm font-black uppercase tracking-[0.18em] text-etc-magenta">Meet The Team</p>
            <h1 class="mt-4 font-heading text-[42px] font-black leading-tight text-[#2a1820] md:text-[56px]">Pengajar Profesional ETC Planet</h1>
            <p class="mx-auto mt-5 max-w-2xl text-[16px] leading-8 text-[#765f67]">Instructor yang tampil di halaman ini berasal dari data user role instructor dengan status aktif dan ditandai tampil di team page.</p>
        </div>
    </section>

    <section class="bg-[#fff0f3] py-20">
        <div class="mx-auto max-w-[1120px] px-5 lg:px-0">
            @if ($instructors->isNotEmpty())
                <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-4">
                    @foreach ($instructors as $teacher)
                        <article class="rounded-[22px] border border-[#eeb8c9] bg-white p-6 text-center shadow-soft">
                            <img src="{{ $assetUrl($teacher->avatar) }}" alt="Foto {{ $teacher->name }}" class="mx-auto h-36 w-36 rounded-full border-4 border-white object-cover shadow-soft">
                            <h2 class="mt-6 font-heading text-xl font-black text-[#2a1820]">{{ $teacher->name }}</h2>
                            <p class="mt-2 font-heading text-sm font-bold text-etc-magenta">{{ $teacher->instructor_position ?? 'Instructor' }}</p>
                            <p class="mt-1 text-sm text-[#765f67]">{{ $teacher->instructor_specialization ?? 'Language Learning' }}</p>
                            <p class="mt-5 text-sm leading-7 text-[#6e5860]">{{ $teacher->instructor_bio ?? 'Bio pengajar akan tampil setelah dilengkapi di data instructor.' }}</p>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="rounded-[22px] border border-dashed border-[#eeb8c9] bg-white p-10 text-center shadow-soft">
                    <span class="material-symbols-outlined text-5xl text-etc-magenta">groups</span>
                    <h2 class="mt-4 font-heading text-2xl font-black text-[#2a1820]">Belum ada instructor yang dipublish</h2>
                    <p class="mt-3 text-[#765f67]">Tambahkan user instructor aktif dengan show_on_team_page agar tampil di sini.</p>
                </div>
            @endif
        </div>
    </section>
</x-layouts.public>
