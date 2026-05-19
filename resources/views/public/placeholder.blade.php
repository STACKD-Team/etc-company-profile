<x-layouts.public :title="$title">
    <section class="bg-etc-surface py-16">
        <div class="mx-auto max-w-[1200px] px-6 lg:px-8">
            <div class="rounded-card bg-white p-8 shadow-soft">
                <p class="font-heading text-sm font-bold uppercase text-etc-magenta">{{ $routeName }}</p>
                <h1 class="mt-2 font-heading text-3xl font-black text-etc-on-surface">{{ $title }}</h1>
                <p class="mt-4 max-w-2xl text-etc-on-muted">
                    Halaman fondasi sudah terhubung ke route dan layout public ETC Planet. Konten dinamis akan ditambahkan pada implementasi fitur.
                </p>
            </div>
        </div>
    </section>
</x-layouts.public>
