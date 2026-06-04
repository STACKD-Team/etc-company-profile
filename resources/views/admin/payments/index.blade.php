@php
    $statusLabels = [
        'pending_payment' => 'Menunggu Verifikasi',
        'paid' => 'Paid',
        'placement_test' => 'Placement Test',
        'enrolled' => 'Enrolled',
        'rejected' => 'Rejected',
        'cancelled' => 'Cancelled',
    ];
    $methods = ['qris' => 'QRIS', 'bank_transfer' => 'Transfer Bank', 'virtual_account' => 'Virtual Account', 'ewallet' => 'E-Wallet', 'manual' => 'Manual'];
@endphp

<x-layouts.dashboard title="Verifikasi Pembayaran" area="admin" active="payments">
    <section class="rounded-card bg-white p-6 shadow-panel">
        @if (session('status'))
            <div class="mb-4 rounded-card bg-green-50 p-3 text-sm font-bold text-green-700">{{ session('status') }}</div>
        @endif

        <form method="GET" class="mb-6 grid gap-3 rounded-card bg-etc-surface-low p-4 lg:grid-cols-6">
            <input name="search" value="{{ request('search') }}" placeholder="Cari nama atau email" class="min-h-11 rounded-xl border border-etc-outline-variant px-4 text-sm lg:col-span-2">
            <select name="status" class="min-h-11 rounded-xl border border-etc-outline-variant px-4 text-sm">
                <option value="">Semua status</option>
                @foreach ($statusLabels as $value => $label)
                    <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <select name="payment_method" class="min-h-11 rounded-xl border border-etc-outline-variant px-4 text-sm">
                <option value="">Semua metode</option>
                @foreach ($methods as $value => $label)
                    <option value="{{ $value }}" @selected(request('payment_method') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <select name="program_id" class="min-h-11 rounded-xl border border-etc-outline-variant px-4 text-sm">
                <option value="">Semua program</option>
                @foreach ($programs as $program)
                    <option value="{{ $program->id }}" @selected((string) request('program_id') === (string) $program->id)>{{ $program->name }}</option>
                @endforeach
            </select>
            <button class="min-h-11 rounded-pill bg-etc-magenta px-5 font-heading text-sm font-bold text-white">Filter</button>
        </form>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="font-heading text-xs uppercase text-etc-on-muted">
                    <tr>
                        <th class="py-3 pr-4">Pendaftar</th>
                        <th class="py-3 pr-4">Program</th>
                        <th class="py-3 pr-4">Metode</th>
                        <th class="py-3 pr-4">Nominal</th>
                        <th class="py-3 pr-4">Status</th>
                        <th class="py-3 pr-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($payments as $payment)
                        <tr class="border-t border-etc-outline-variant/60">
                            <td class="py-4 pr-4">
                                <p class="font-heading font-bold">{{ $payment->applicant_name }}</p>
                                <p class="text-xs text-etc-on-muted">{{ $payment->registration_code }}</p>
                            </td>
                            <td class="py-4 pr-4">{{ $payment->program?->name ?? '-' }}</td>
                            <td class="py-4 pr-4">{{ $methods[$payment->payment_method] ?? ($payment->payment_method ?? '-') }}</td>
                            <td class="py-4 pr-4">{{ $payment->payment_amount ? 'Rp '.number_format((float) $payment->payment_amount, 0, ',', '.') : '-' }}</td>
                            <td class="py-4 pr-4"><span class="rounded-pill bg-etc-surface-container px-3 py-1 text-xs font-bold text-etc-on-surface">{{ $statusLabels[$payment->status] ?? $payment->status }}</span></td>
                            <td class="py-4 pr-4"><a href="{{ route('admin.payments.show', ['payment' => $payment]) }}" class="font-heading text-sm font-bold text-etc-magenta">Detail</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="py-8 text-center text-etc-on-muted">Belum ada pembayaran untuk diverifikasi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-5">{{ $payments->links() }}</div>
    </section>
</x-layouts.dashboard>
