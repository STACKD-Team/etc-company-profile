<x-layouts.public title="Masuk">
    <section class="bg-etc-surface py-16">
        <div class="mx-auto grid max-w-[1000px] gap-8 px-6 lg:grid-cols-[0.9fr_1.1fr] lg:px-8">
            <div class="flex flex-col justify-center">
                <p class="font-heading text-sm font-bold uppercase text-etc-magenta">Akses akun</p>
                <h1 class="mt-3 font-heading text-4xl font-black leading-tight text-etc-on-surface">Masuk ke dashboard ETC Planet.</h1>
                <p class="mt-5 text-base leading-7 text-etc-on-muted">
                    Admin dapat mengelola operasional, sedangkan siswa dan instructor akan diarahkan ke area masing-masing saat modulnya tersedia.
                </p>
            </div>

            <form method="POST" action="{{ route('auth.login.store') }}" class="rounded-card bg-white p-8 shadow-panel">
                @csrf

                <div>
                    <label for="email" class="font-heading text-sm font-bold text-etc-on-surface">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus class="mt-2 w-full rounded-lg border border-etc-outline-variant px-4 py-3 text-etc-on-surface outline-none transition focus:border-etc-magenta focus:ring-4 focus:ring-etc-magenta/10">
                    @error('email')
                        <p class="mt-2 text-sm text-red-700">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-5">
                    <label for="password" class="font-heading text-sm font-bold text-etc-on-surface">Password</label>
                    <input id="password" name="password" type="password" required class="mt-2 w-full rounded-lg border border-etc-outline-variant px-4 py-3 text-etc-on-surface outline-none transition focus:border-etc-magenta focus:ring-4 focus:ring-etc-magenta/10">
                    @error('password')
                        <p class="mt-2 text-sm text-red-700">{{ $message }}</p>
                    @enderror
                </div>

                <label class="mt-5 flex items-center gap-3 text-sm text-etc-on-muted">
                    <input type="checkbox" name="remember" value="1" class="h-5 w-5 rounded border-etc-outline-variant text-etc-magenta focus:ring-etc-magenta">
                    Ingat saya
                </label>

                <button type="submit" class="mt-7 inline-flex min-h-12 w-full items-center justify-center rounded-full bg-etc-magenta px-5 py-3 font-heading text-sm font-bold text-white transition hover:bg-etc-primary">
                    Masuk
                </button>
            </form>
        </div>
    </section>
</x-layouts.public>
