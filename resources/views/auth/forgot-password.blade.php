<x-layouts.public title="Lupa Password" :show-navbar="false" :show-footer="false" :show-chatbot="false">
    <x-public-discovery.navbar />
    <section class="bg-etc-surface py-16">
        <div class="mx-auto max-w-xl px-6 lg:px-8">
            <form method="POST" action="{{ route('auth.password.email') }}" class="rounded-card bg-white p-8 shadow-panel">
                @csrf
                <h1 class="font-heading text-3xl font-black text-etc-on-surface">Lupa password</h1>
                <p class="mt-3 text-sm leading-6 text-etc-on-muted">Masukkan email akun untuk menerima link reset password.</p>

                @if (session('status'))
                    <div class="mt-5 rounded-lg bg-etc-surface-container p-4 text-sm text-etc-on-surface">{{ session('status') }}</div>
                @endif

                <label for="email" class="mt-6 block font-heading text-sm font-bold text-etc-on-surface">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus class="mt-2 w-full rounded-lg border border-etc-outline-variant px-4 py-3 outline-none transition focus:border-etc-magenta focus:ring-4 focus:ring-etc-magenta/10">
                @error('email')
                    <p class="mt-2 text-sm text-red-700">{{ $message }}</p>
                @enderror

                <button type="submit" class="mt-7 inline-flex min-h-12 w-full items-center justify-center rounded-full bg-etc-magenta px-5 py-3 font-heading text-sm font-bold text-white transition hover:bg-etc-primary">
                    Kirim Link Reset
                </button>
            </form>
        </div>
    </section>
    <x-public-discovery.page-end />
</x-layouts.public>
