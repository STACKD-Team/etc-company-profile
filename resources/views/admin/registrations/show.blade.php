@php
    $statusLabel = str($registration->status)->replace('_', ' ')->headline()->toString();
    $identity = [
        'Nama' => $registration->applicant_name,
        'Email' => $registration->applicant_email,
        'No HP' => $registration->applicant_phone,
        'Akun Siswa' => $registration->user?->full_name ?? $registration->user?->name ?? '-',
    ];
    $program = [
        'Program' => $registration->program?->name ?? '-',
        'Preferensi Hari' => str($registration->preferred_days ?? '-')->replace('_', ' ')->headline()->toString(),
        'Preferensi Jam' => $registration->preferred_time ?? '-',
        'Kelas Assigned' => $registration->courseClass?->name ?? '-',
    ];
    $payment = [
        'Metode' => $registration->payment_method ? str($registration->payment_method)->replace('_', ' ')->headline()->toString() : '-',
        'Nominal' => $registration->payment_amount ? 'Rp '.number_format((float) $registration->payment_amount, 0, ',', '.') : '-',
        'Gateway ID' => $registration->payment_gateway_id ?? '-',
        'Dibayar Pada' => $registration->paid_at?->format('d M Y H:i') ?? '-',
    ];
    $placement = [
        'Jadwal Placement' => $registration->placement_test_at?->format('d M Y H:i') ?? '-',
        'Hasil Placement' => $registration->placement_test_result ?? '-',
        'Tanggal Submit' => $registration->created_at?->format('d M Y H:i') ?? '-',
        'Update Terakhir' => $registration->updated_at?->format('d M Y H:i') ?? '-',
    ];
@endphp

<x-layouts.dashboard title="Detail Pendaftaran" area="admin" active="registrations">
    @if (session('status'))
        <x-ui.alert status="success" class="mb-5">{{ session('status') }}</x-ui.alert>
    @endif

    <div class="space-y-6">
        <x-ui.panel>
            <div class="flex flex-col justify-between gap-4 md:flex-row md:items-start">
                <div>
                    <p class="font-heading text-xs font-bold uppercase text-etc-magenta">Pendaftaran</p>
                    <h2 class="mt-2 font-heading text-2xl font-black text-etc-on-surface">{{ $registration->registration_code }}</h2>
                    <p class="mt-2 text-sm text-etc-on-muted">{{ $registration->created_at?->format('d M Y H:i') ?? '-' }}</p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <x-ui.badge :status="$registration->status">{{ $statusLabel }}</x-ui.badge>
                    <x-ui.button :href="route('admin.payments.show', ['payment' => $registration])" outlined>Pembayaran</x-ui.button>
                    <x-ui.button :href="route('admin.registrations.edit', $registration)">Edit</x-ui.button>
                </div>
            </div>
        </x-ui.panel>

        <div class="grid gap-6 xl:grid-cols-2">
            @foreach (['Identitas Pendaftar' => $identity, 'Program dan Jadwal' => $program, 'Pembayaran' => $payment, 'Placement dan Catatan' => $placement] as $heading => $items)
                <x-ui.panel :heading="$heading">
                    <dl class="divide-y-2 divide-etc-outline-variant/60">
                        @foreach ($items as $label => $value)
                            <div class="grid gap-1 py-3 md:grid-cols-[150px_1fr] md:gap-4">
                                <dt class="font-heading text-xs font-bold uppercase text-etc-on-muted">{{ $label }}</dt>
                                <dd class="whitespace-pre-line text-sm text-etc-on-surface">{{ $value }}</dd>
                            </div>
                        @endforeach
                    </dl>
                </x-ui.panel>
            @endforeach
        </div>

        <x-ui.panel heading="Catatan Internal">
            <p class="whitespace-pre-line text-sm leading-6 text-etc-on-muted">{{ $registration->notes ?: 'Tidak ada catatan.' }}</p>
        </x-ui.panel>
    </div>
</x-layouts.dashboard>
