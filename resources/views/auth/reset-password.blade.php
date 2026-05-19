<x-layouts.public title="Reset Password">
    <section class="bg-etc-surface py-16">
        <div class="mx-auto max-w-xl px-6 lg:px-8">
            <form method="POST" action="{{ route('auth.password.update') }}" class="rounded-card bg-white p-8 shadow-panel">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <h1 class="font-heading text-3xl font-black text-etc-on-surface">Reset password</h1>
                <p class="mt-3 text-sm leading-6 text-etc-on-muted">Buat password baru untuk akun ETC Planet.</p>

                <label for="email" class="mt-6 block font-heading text-sm font-bold text-etc-on-surface">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email', $email) }}" required class="mt-2 w-full rounded-lg border border-etc-outline-variant px-4 py-3 outline-none transition focus:border-etc-magenta focus:ring-4 focus:ring-etc-magenta/10">
                @error('email')
                    <p class="mt-2 text-sm text-red-700">{{ $message }}</p>
                @enderror

                <label for="password" class="mt-5 block font-heading text-sm font-bold text-etc-on-surface">Password baru</label>
                <input id="password" name="password" type="password" required class="mt-2 w-full rounded-lg border border-etc-outline-variant px-4 py-3 outline-none transition focus:border-etc-magenta focus:ring-4 focus:ring-etc-magenta/10">
                @error('password')
                    <p class="mt-2 text-sm text-red-700">{{ $message }}</p>
                @enderror

                <label for="password_confirmation" class="mt-5 block font-heading text-sm font-bold text-etc-on-surface">Konfirmasi password</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required class="mt-2 w-full rounded-lg border border-etc-outline-variant px-4 py-3 outline-none transition focus:border-etc-magenta focus:ring-4 focus:ring-etc-magenta/10">

                <button type="submit" class="mt-7 inline-flex min-h-12 w-full items-center justify-center rounded-full bg-etc-magenta px-5 py-3 font-heading text-sm font-bold text-white transition hover:bg-etc-primary">
                    Simpan Password
                </button>
            </form>
        </div>
    </section>
</x-layouts.public>
