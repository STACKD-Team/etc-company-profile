<x-layouts.public title="Lupa Password" :show-navbar="false" :show-footer="false" :show-chatbot="false">
    <x-public-discovery.navbar />
    <section class="bg-etc-surface py-16">
        <div class="mx-auto max-w-xl px-6 lg:px-8">
            <form method="POST" action="{{ route('auth.password.email') }}" class="rounded-card bg-etc-surface p-8 shadow-panel ring-2 ring-etc-outline-variant">
                @csrf
                <h1 class="font-heading text-3xl font-black text-etc-on-surface">Lupa password</h1>
                <p class="mt-3 text-sm leading-6 text-etc-on-muted">Masukkan email akun untuk menerima link reset password.</p>

                @if (session('status'))
                    <x-ui.alert status="success" class="mt-5">{{ session('status') }}</x-ui.alert>
                @endif

                <div class="mt-6">
                    <x-ui.email-field name="email" label="Email" required autofocus size="lg" />
                </div>

                <x-ui.button type="submit" size="xl" class="mt-7 w-full">
                    Kirim Link Reset
                </x-ui.button>
            </form>
        </div>
    </section>
    <x-public-discovery.page-end />
</x-layouts.public>
