<x-layouts.public title="Masuk" :show-navbar="false" :show-footer="false" :show-chatbot="false">
    <x-public-discovery.navbar />
    <section class="bg-etc-surface py-16">
        <div class="mx-auto grid max-w-[1000px] gap-8 px-6 lg:grid-cols-[0.9fr_1.1fr] lg:px-8">
            <div class="flex flex-col justify-center">
                <p class="font-heading text-sm font-bold uppercase text-etc-magenta">Akses akun</p>
                <h1 class="mt-3 font-heading text-4xl font-black leading-tight text-etc-on-surface">Masuk ke dashboard ETC Planet.</h1>
                <p class="mt-5 text-base leading-7 text-etc-on-muted">
                    Admin dapat mengelola operasional, sedangkan siswa dan instructor akan diarahkan ke area masing-masing saat modulnya tersedia.
                </p>
            </div>

            <form method="POST" action="{{ route('auth.login.store') }}" class="rounded-card bg-etc-surface p-8 shadow-panel ring-2 ring-etc-outline-variant">
                @csrf

                <x-ui.email-field name="email" label="Email" required autofocus size="lg" />

                <div class="mt-5">
                    <x-ui.password-field name="password" label="Password" required size="lg" />
                </div>

                <div class="mt-5">
                    <x-ui.checkbox name="remember" label="Ingat saya" />
                </div>

                <x-ui.button type="submit" size="xl" class="mt-7 w-full">
                    Masuk
                </x-ui.button>
            </form>
        </div>
    </section>
    <x-public-discovery.page-end />
</x-layouts.public>
