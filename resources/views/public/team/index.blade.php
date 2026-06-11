<x-layouts.public title="Team Pengajar" navbar-active="about">
    @php
        $media = app(\App\Services\PublicDiscoveryService::class);
        $assetUrl = static fn (?string $path) => $media->mediaUrl($path, 'images/Ms. Debby.jpeg');
    @endphp

    <section class="public-section bg-etc-surface">
        <div class="public-shell-narrow text-center public-reveal" data-public-reveal>
            <p class="public-eyebrow">Meet The Team</p>
            <h1 class="public-title mt-4">Pengajar profesional ETC Planet</h1>
            <p class="public-subtitle mx-auto mt-5 max-w-2xl">
                Belajar bersama pengajar yang ramah, aktif memberi feedback, dan terbiasa mendampingi siswa dari berbagai level.
            </p>
        </div>
    </section>

    <section class="public-section bg-etc-surface-low">
        <div class="public-shell">
            @if ($instructors->isNotEmpty())
                <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-4">
                    @foreach ($instructors as $teacher)
                        <article class="public-card p-5 text-center public-reveal" data-public-reveal>
                            <img src="{{ $assetUrl($teacher->avatar) }}" alt="Foto {{ $teacher->name }}" class="mx-auto h-32 w-32 rounded-full border-2 border-etc-outline-variant object-cover shadow-soft">
                            <h2 class="mt-5 font-heading text-lg font-bold">{{ $teacher->name }}</h2>
                            <p class="mt-2 font-heading text-sm font-bold text-etc-magenta">{{ $teacher->instructor_position ?? 'Instructor' }}</p>
                            <p class="mt-1 text-sm text-etc-on-muted">{{ $teacher->instructor_specialization ?? 'Language Learning' }}</p>
                            <p class="mt-4 text-sm leading-7 text-etc-on-muted">{{ $teacher->instructor_bio ?? 'Bio pengajar akan tampil setelah dilengkapi di data instructor.' }}</p>
                        </article>
                    @endforeach
                </div>
            @else
                <x-ui.empty-state
                    heading="Profil pengajar belum tersedia"
                    description="Tim pengajar pilihan ETC Planet akan tampil di sini setelah profilnya siap."
                    icon="heroicon-o-users"
                    contained
                />
            @endif
        </div>
    </section>
</x-layouts.public>
