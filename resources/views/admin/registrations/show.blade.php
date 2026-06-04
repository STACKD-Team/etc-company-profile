@php
    $statusLabel = str($registration->status)->replace('_', ' ')->headline()->toString();
    $details = [
        'Kode Pendaftaran' => $registration->registration_code,
        'Nama' => $registration->applicant_name,
        'Email' => $registration->applicant_email,
        'No HP' => $registration->applicant_phone,
        'Program' => $registration->program?->name ?? '-',
        'Preferensi Hari' => $registration->preferred_days ?? '-',
        'Preferensi Jam' => $registration->preferred_time ?? '-',
        'Metode Pembayaran' => $registration->payment_method ?? '-',
        'Nominal' => $registration->payment_amount ? 'Rp '.number_format((float) $registration->payment_amount, 0, ',', '.') : '-',
        'Status' => $statusLabel,
        'Dibayar Pada' => $registration->paid_at?->format('d M Y H:i') ?? '-',
        'Catatan' => $registration->notes ?? '-',
    ];
@endphp

<x-layouts.dashboard title="Detail Pendaftaran" area="admin" active="registrations">
    @if (session('status'))
        <div class="mb-5 rounded-card bg-green-50 p-3 text-sm font-bold text-green-700">{{ session('status') }}</div>
    @endif

    <section class="rounded-card bg-white p-6 shadow-panel">
        <div class="mb-6 flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="font-heading text-xs font-bold uppercase text-etc-magenta">Pendaftaran</p>
                <h2 class="mt-2 font-heading text-2xl font-bold">{{ $registration->registration_code }}</h2>
                <p class="mt-1 text-sm text-etc-on-muted">{{ $registration->created_at?->format('d M Y H:i') }}</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.payments.show', ['payment' => $registration]) }}" class="rounded-pill border border-etc-outline-variant px-5 py-3 font-heading text-sm font-bold text-etc-on-surface">Pembayaran</a>
                <a href="{{ route('admin.registrations.edit', $registration) }}" class="rounded-pill bg-etc-magenta px-5 py-3 font-heading text-sm font-bold text-white">Edit</a>
            </div>
        </div>

        <dl class="grid gap-4 md:grid-cols-2">
            @foreach ($details as $label => $value)
                <div class="rounded-lg bg-etc-surface p-4">
                    <dt class="font-heading text-xs font-bold uppercase text-etc-on-muted">{{ $label }}</dt>
                    <dd class="mt-2 text-sm text-etc-on-surface">{{ $value }}</dd>
                </div>
            @endforeach
        </dl>
    </section>
</x-layouts.dashboard>
