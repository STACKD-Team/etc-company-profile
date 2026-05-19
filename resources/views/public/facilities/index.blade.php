<x-layouts.public title="Fasilitas">
    @php($assetUrl = static fn (?string $path) => asset($path ?: 'images/pu1-img.jpg'))

    <section class="bg-[#fff8f8] py-20">
        <div class="mx-auto max-w-[1120px] px-5 lg:px-0">
            <p class="font-heading text-sm font-black uppercase tracking-[0.18em] text-etc-magenta">Fasilitas</p>
            <h1 class="mt-4 max-w-3xl font-heading text-[42px] font-black leading-tight text-[#2a1820] md:text-[56px]">Ruang belajar yang nyaman dan fokus</h1>
            <p class="mt-5 max-w-2xl text-[16px] leading-8 text-[#765f67]">Data fasilitas diambil dari CMS contents type room.</p>
        </div>
    </section>

    <section class="bg-white py-20">
        <div class="mx-auto max-w-[1120px] px-5 lg:px-0">
            @if ($rooms->isNotEmpty())
                <div class="grid gap-8 md:grid-cols-3">
                    @foreach ($rooms as $room)
                        <article class="overflow-hidden rounded-[22px] border border-[#eeb8c9] bg-white shadow-soft">
                            <img src="{{ $assetUrl($room->image) }}" alt="{{ $room->title }}" class="h-56 w-full object-cover">
                            <div class="p-6">
                                <h2 class="font-heading text-2xl font-black text-[#2a1820]">{{ $room->title }}</h2>
                                <p class="mt-3 min-h-20 text-sm leading-7 text-[#765f67]">{{ $room->body }}</p>
                                <div class="mt-5 flex flex-wrap gap-2">
                                    @foreach (($room->meta['facility'] ?? []) as $facility)
                                        <span class="rounded-full bg-[#ffe6f3] px-3 py-1 font-heading text-xs font-bold text-[#2a1820]">{{ $facility }}</span>
                                    @endforeach
                                </div>
                                @if ($room->meta['capacity'] ?? null)
                                    <p class="mt-5 flex items-center gap-2 text-sm font-bold text-etc-magenta">
                                        <span class="material-symbols-outlined text-base">groups</span>
                                        Kapasitas {{ $room->meta['capacity'] }} siswa
                                    </p>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="rounded-[22px] border border-dashed border-[#eeb8c9] bg-[#fff8f8] p-10 text-center">
                    <span class="material-symbols-outlined text-5xl text-etc-magenta">meeting_room</span>
                    <h2 class="mt-4 font-heading text-2xl font-black text-[#2a1820]">Data fasilitas belum dipublish</h2>
                    <p class="mt-3 text-[#765f67]">Tambahkan content type room untuk menampilkan fasilitas.</p>
                </div>
            @endif
        </div>
    </section>
</x-layouts.public>
