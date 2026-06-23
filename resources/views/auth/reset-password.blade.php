<x-layouts.public title="Reset Password" :show-navbar="false" :show-footer="false" :show-chatbot="false">
    <x-public-discovery.navbar />
    <section class="bg-etc-surface py-16">
        <div class="mx-auto max-w-xl px-6 lg:px-8">
            <form method="POST" action="{{ route('auth.password.update') }}" class="rounded-card bg-etc-surface p-8 shadow-panel ring-2 ring-etc-outline-variant">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <h1 class="font-heading text-3xl font-black text-etc-on-surface">Reset password</h1>
                <p class="mt-3 text-sm leading-6 text-etc-on-muted">Buat password baru untuk akun ETC Planet.</p>

                <div class="mt-6">
                    <x-ui.email-field name="email" label="Email" :value="$email" required size="lg" />
                </div>

                <div class="mt-5">
                    <x-ui.password-field name="password" label="Password baru" required size="lg" />
                </div>

                <div class="mt-5">
                    <x-ui.password-field name="password_confirmation" label="Konfirmasi password" required size="lg" />
                </div>

                <x-ui.button type="submit" size="xl" class="mt-7 w-full">
                    Simpan Password
                </x-ui.button>
            </form>
        </div>
    </section>
    <x-public-discovery.page-end />
</x-layouts.public>
