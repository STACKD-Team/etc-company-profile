<x-layouts.dashboard title="Settings" area="admin" active="settings">
    @php
        $value = static fn (string $key, ?string $fallback = null): ?string => old($key, $settings->get($key)?->meta['value'] ?? $fallback);
        $qris = $settings->get('qris');
        $qrisUrl = $qris?->image
            ? (\Illuminate\Support\Str::startsWith($qris->image, ['http://', 'https://', '/', 'images/', 'storage/'])
                ? asset(ltrim($qris->image, '/'))
                : \Illuminate\Support\Facades\Storage::url($qris->image))
            : null;
    @endphp

    <section class="space-y-5">
        @if (session('status'))
            <div class="rounded-card border border-green-200 bg-green-50 px-5 py-4 font-heading text-sm font-bold text-green-700">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" class="grid gap-6 lg:grid-cols-[1fr_320px]">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <div class="rounded-card bg-white p-6 shadow-panel">
                    <h2 class="font-heading text-lg font-black">Kontak dan Sosial</h2>
                    <div class="mt-5 grid gap-5 md:grid-cols-2">
                        <label class="md:col-span-2">
                            <span class="font-heading text-sm font-bold">Alamat</span>
                            <textarea name="address" rows="3" class="mt-2 w-full rounded-xl border border-etc-outline-variant px-4 py-3 text-sm focus:border-etc-magenta focus:outline-none">{{ $value('address', 'Jl. S. Parman No. 202B, Padang') }}</textarea>
                            @error('address')<span class="mt-1 block text-xs font-bold text-red-600">{{ $message }}</span>@enderror
                        </label>
                        <label>
                            <span class="font-heading text-sm font-bold">Telepon</span>
                            <input name="phone" value="{{ $value('phone', '+62 812-0000-0000') }}" class="mt-2 min-h-12 w-full rounded-xl border border-etc-outline-variant px-4 text-sm focus:border-etc-magenta focus:outline-none">
                            @error('phone')<span class="mt-1 block text-xs font-bold text-red-600">{{ $message }}</span>@enderror
                        </label>
                        <label>
                            <span class="font-heading text-sm font-bold">Email</span>
                            <input name="email" value="{{ $value('email', 'hello@etcplanet.test') }}" class="mt-2 min-h-12 w-full rounded-xl border border-etc-outline-variant px-4 text-sm focus:border-etc-magenta focus:outline-none">
                            @error('email')<span class="mt-1 block text-xs font-bold text-red-600">{{ $message }}</span>@enderror
                        </label>
                        <label>
                            <span class="font-heading text-sm font-bold">Instagram</span>
                            <input name="instagram" value="{{ $value('instagram') }}" class="mt-2 min-h-12 w-full rounded-xl border border-etc-outline-variant px-4 text-sm focus:border-etc-magenta focus:outline-none">
                            @error('instagram')<span class="mt-1 block text-xs font-bold text-red-600">{{ $message }}</span>@enderror
                        </label>
                        <label>
                            <span class="font-heading text-sm font-bold">Jam Operasional</span>
                            <input name="hours" value="{{ $value('hours', 'Senin-Sabtu, 09.00-18.30') }}" class="mt-2 min-h-12 w-full rounded-xl border border-etc-outline-variant px-4 text-sm focus:border-etc-magenta focus:outline-none">
                            @error('hours')<span class="mt-1 block text-xs font-bold text-red-600">{{ $message }}</span>@enderror
                        </label>
                    </div>
                </div>

                <div class="rounded-card bg-white p-6 shadow-panel">
                    <h2 class="font-heading text-lg font-black">Pembayaran</h2>
                    <div class="mt-5 grid gap-5 md:grid-cols-2">
                        <label>
                            <span class="font-heading text-sm font-bold">Nama Bank</span>
                            <input name="bank_name" value="{{ $value('bank_name') }}" class="mt-2 min-h-12 w-full rounded-xl border border-etc-outline-variant px-4 text-sm focus:border-etc-magenta focus:outline-none">
                            @error('bank_name')<span class="mt-1 block text-xs font-bold text-red-600">{{ $message }}</span>@enderror
                        </label>
                        <label>
                            <span class="font-heading text-sm font-bold">Nomor Rekening</span>
                            <input name="bank_account_number" value="{{ $value('bank_account_number') }}" class="mt-2 min-h-12 w-full rounded-xl border border-etc-outline-variant px-4 text-sm focus:border-etc-magenta focus:outline-none">
                            @error('bank_account_number')<span class="mt-1 block text-xs font-bold text-red-600">{{ $message }}</span>@enderror
                        </label>
                        <label class="md:col-span-2">
                            <span class="font-heading text-sm font-bold">Nama Pemilik Rekening</span>
                            <input name="bank_account_name" value="{{ $value('bank_account_name') }}" class="mt-2 min-h-12 w-full rounded-xl border border-etc-outline-variant px-4 text-sm focus:border-etc-magenta focus:outline-none">
                            @error('bank_account_name')<span class="mt-1 block text-xs font-bold text-red-600">{{ $message }}</span>@enderror
                        </label>
                        <label class="md:col-span-2">
                            <span class="font-heading text-sm font-bold">Catatan Pembayaran</span>
                            <textarea name="payment_notes" rows="4" class="mt-2 w-full rounded-xl border border-etc-outline-variant px-4 py-3 text-sm focus:border-etc-magenta focus:outline-none">{{ $value('payment_notes') }}</textarea>
                            @error('payment_notes')<span class="mt-1 block text-xs font-bold text-red-600">{{ $message }}</span>@enderror
                        </label>
                    </div>
                </div>
            </div>

            <aside class="space-y-5">
                <div class="rounded-card bg-etc-charcoal p-5 text-white shadow-panel">
                    <p class="font-heading text-sm font-black uppercase text-etc-magenta">QRIS</p>
                    <div class="mt-4 overflow-hidden rounded-2xl bg-white p-3">
                        @if ($qrisUrl)
                            <img src="{{ $qrisUrl }}" alt="QRIS ETC Planet" class="aspect-square w-full rounded-xl object-contain">
                        @else
                            <div class="flex aspect-square items-center justify-center rounded-xl bg-etc-surface-container text-etc-magenta">
                                <span class="material-symbols-outlined text-6xl">qr_code_2</span>
                            </div>
                        @endif
                    </div>
                    <input type="file" name="qris" accept="image/*" class="mt-4 w-full rounded-xl border border-white/15 px-4 py-3 text-sm text-white file:mr-4 file:rounded-pill file:border-0 file:bg-etc-magenta file:px-4 file:py-2 file:font-heading file:text-xs file:font-bold file:text-white">
                    @error('qris')<span class="mt-1 block text-xs font-bold text-red-200">{{ $message }}</span>@enderror
                </div>

                <button class="inline-flex min-h-12 w-full items-center justify-center gap-2 rounded-pill bg-etc-magenta px-6 font-heading text-sm font-bold text-white shadow-soft">
                    <span class="material-symbols-outlined text-base">save</span>
                    Simpan Settings
                </button>
            </aside>
        </form>
    </section>
</x-layouts.dashboard>
