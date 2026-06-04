@php
    $methods = ['qris' => 'QRIS', 'bank_transfer' => 'Transfer Bank', 'virtual_account' => 'Virtual Account', 'ewallet' => 'E-Wallet', 'manual' => 'Manual'];
    $proofUrl = $payment->payment_proof ? \Illuminate\Support\Facades\Storage::url($payment->payment_proof) : null;
@endphp

<x-layouts.dashboard title="Detail Pembayaran" area="admin" active="payments">
    @if (session('status'))
        <div class="mb-5 rounded-card bg-green-50 p-3 text-sm font-bold text-green-700">{{ session('status') }}</div>
    @endif

    <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_360px]">
        <section class="rounded-card bg-white p-6 shadow-panel">
            <div class="mb-6 flex flex-wrap items-start justify-between gap-4">
                <div>
                    <p class="font-heading text-xs font-bold uppercase text-etc-magenta">Pembayaran</p>
                    <h2 class="mt-2 font-heading text-2xl font-bold">{{ $payment->registration_code }}</h2>
                    <p class="mt-1 text-sm text-etc-on-muted">{{ $payment->applicant_name }} - {{ $payment->program?->name ?? '-' }}</p>
                </div>
                <a href="{{ route('admin.registrations.show', ['registration' => $payment]) }}" class="rounded-pill border border-etc-outline-variant px-5 py-3 font-heading text-sm font-bold text-etc-on-surface">Detail Pendaftaran</a>
            </div>

            <dl class="grid gap-4 md:grid-cols-2">
                <div class="rounded-lg bg-etc-surface p-4">
                    <dt class="font-heading text-xs font-bold uppercase text-etc-on-muted">Metode</dt>
                    <dd class="mt-2 text-sm">{{ $methods[$payment->payment_method] ?? ($payment->payment_method ?? '-') }}</dd>
                </div>
                <div class="rounded-lg bg-etc-surface p-4">
                    <dt class="font-heading text-xs font-bold uppercase text-etc-on-muted">Nominal</dt>
                    <dd class="mt-2 text-sm">{{ $payment->payment_amount ? 'Rp '.number_format((float) $payment->payment_amount, 0, ',', '.') : '-' }}</dd>
                </div>
                <div class="rounded-lg bg-etc-surface p-4">
                    <dt class="font-heading text-xs font-bold uppercase text-etc-on-muted">Status</dt>
                    <dd class="mt-2 text-sm">{{ str($payment->status)->replace('_', ' ')->headline() }}</dd>
                </div>
                <div class="rounded-lg bg-etc-surface p-4">
                    <dt class="font-heading text-xs font-bold uppercase text-etc-on-muted">Paid At</dt>
                    <dd class="mt-2 text-sm">{{ $payment->paid_at?->format('d M Y H:i') ?? '-' }}</dd>
                </div>
                <div class="rounded-lg bg-etc-surface p-4 md:col-span-2">
                    <dt class="font-heading text-xs font-bold uppercase text-etc-on-muted">Catatan</dt>
                    <dd class="mt-2 whitespace-pre-line text-sm">{{ $payment->notes ?? '-' }}</dd>
                </div>
            </dl>

            <div class="mt-6 rounded-card border border-etc-outline-variant p-4">
                <p class="font-heading text-sm font-bold">Bukti Pembayaran</p>
                @if ($proofUrl)
                    <a href="{{ $proofUrl }}" target="_blank" class="mt-3 inline-flex rounded-pill bg-etc-charcoal px-5 py-3 font-heading text-sm font-bold text-white">Buka Bukti Upload</a>
                @else
                    <p class="mt-2 text-sm text-etc-on-muted">Belum ada bukti pembayaran yang diupload.</p>
                @endif
            </div>
        </section>

        <aside class="space-y-6">
            <section class="rounded-card bg-white p-6 shadow-panel">
                <h3 class="font-heading text-lg font-bold">Verifikasi</h3>
                <form method="POST" action="{{ route('admin.payments.verify', ['payment' => $payment]) }}" class="mt-4 space-y-4">
                    @csrf
                    <label class="block space-y-2">
                        <span class="font-heading text-sm font-bold">Nominal Koreksi</span>
                        <input name="payment_amount" type="number" min="0" value="{{ old('payment_amount', $payment->payment_amount) }}" class="min-h-11 w-full rounded-xl border border-etc-outline-variant px-4 text-sm">
                        @error('payment_amount')<span class="text-xs font-bold text-red-600">{{ $message }}</span>@enderror
                    </label>
                    <label class="block space-y-2">
                        <span class="font-heading text-sm font-bold">Metode</span>
                        <select name="payment_method" class="min-h-11 w-full rounded-xl border border-etc-outline-variant px-4 text-sm">
                            <option value="">Tidak diubah</option>
                            @foreach ($methods as $value => $label)
                                <option value="{{ $value }}" @selected(old('payment_method', $payment->payment_method) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('payment_method')<span class="text-xs font-bold text-red-600">{{ $message }}</span>@enderror
                    </label>
                    <button class="w-full rounded-pill bg-etc-magenta px-5 py-3 font-heading text-sm font-bold text-white">Verifikasi Paid</button>
                </form>
            </section>

            <section class="rounded-card bg-white p-6 shadow-panel">
                <h3 class="font-heading text-lg font-bold">Tolak Pembayaran</h3>
                <form method="POST" action="{{ route('admin.payments.reject', ['payment' => $payment]) }}" class="mt-4 space-y-4">
                    @csrf
                    <label class="block space-y-2">
                        <span class="font-heading text-sm font-bold">Alasan</span>
                        <textarea name="notes" rows="4" class="w-full rounded-xl border border-etc-outline-variant px-4 py-3 text-sm">{{ old('notes') }}</textarea>
                        @error('notes')<span class="text-xs font-bold text-red-600">{{ $message }}</span>@enderror
                    </label>
                    <button class="w-full rounded-pill border border-red-200 px-5 py-3 font-heading text-sm font-bold text-red-700">Tolak</button>
                </form>
            </section>
        </aside>
    </div>
</x-layouts.dashboard>
