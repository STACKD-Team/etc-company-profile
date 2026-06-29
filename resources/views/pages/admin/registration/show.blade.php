@php
    $statusLabel = str($registration->status)->replace('_', ' ')->headline()->toString();
@endphp

<x-layouts.dashboard title="Detail Pendaftaran" area="admin" active="registrations">
    @if (session('status'))
        <x-ui.alert status="success" class="mb-5">{{ session('status') }}</x-ui.alert>
    @endif

    <x-ui.resource-header
        :title="$registration->registration_code"
        :subtitle="$registration->applicant_name.' - '.($registration->created_at?->format('d M Y H:i') ?? '-')"
        :back-url="route('admin.registration.index')"
    >
        <x-slot:status>
            <x-ui.badge :status="$registration->status">{{ $statusLabel }}</x-ui.badge>
        </x-slot:status>
        <x-slot:actions>
            <x-ui.button :href="route('admin.payment.show', ['payment' => $registration])" outlined icon="heroicon-m-banknotes">Pembayaran</x-ui.button>
            <x-ui.button :href="route('admin.registration.edit', $registration)" icon="heroicon-m-pencil-square">Edit</x-ui.button>
            <x-ui.delete-action :action="route('admin.registration.destroy', $registration)" heading="Hapus pendaftaran?" />
        </x-slot:actions>
    </x-ui.resource-header>

    <div class="grid gap-6 xl:grid-cols-2">
        <x-ui.detail-card heading="Identitas Pendaftar">
            <x-ui.description-list>
                <x-ui.description-item label="Nama" :value="$registration->applicant_name" />
                <x-ui.description-item label="Email" :value="$registration->applicant_email" />
                <x-ui.description-item label="No HP" :value="$registration->applicant_phone" />
                <x-ui.description-item label="Akun Siswa" :value="$registration->user?->full_name ?? $registration->user?->name ?? '-'" />
            </x-ui.description-list>
        </x-ui.detail-card>

        <x-ui.detail-card heading="Program dan Jadwal">
            <x-ui.description-list>
                <x-ui.description-item label="Program" :value="$registration->program?->name ?? '-'" />
                <x-ui.description-item label="Preferensi Hari" :value="str($registration->preferred_days ?? '-')->replace('_', ' ')->headline()" />
                <x-ui.description-item label="Preferensi Jam" :value="$registration->preferred_time ?? '-'" />
                <x-ui.description-item label="Kelas Assigned" :value="$registration->courseClass?->name ?? '-'" />
            </x-ui.description-list>
        </x-ui.detail-card>

        <x-ui.detail-card heading="Pembayaran">
            <x-ui.description-list>
                <x-ui.description-item label="Metode" :value="$registration->payment_method ? str($registration->payment_method)->replace('_', ' ')->headline() : '-'" />
                <x-ui.description-item label="Nominal" :value="$registration->payment_amount ? 'Rp '.number_format((float) $registration->payment_amount, 0, ',', '.') : '-'" />
                <x-ui.description-item label="Gateway ID" :value="$registration->payment_gateway_id ?? '-'" />
                <x-ui.description-item label="Dibayar Pada" :value="$registration->paid_at?->format('d M Y H:i') ?? '-'" />
            </x-ui.description-list>
        </x-ui.detail-card>

        <x-ui.detail-card heading="Placement dan Catatan">
            <x-ui.description-list>
                <x-ui.description-item label="Jadwal Placement" :value="$registration->placement_test_at?->format('d M Y H:i') ?? '-'" />
                <x-ui.description-item label="Hasil Placement" :value="$registration->placement_test_result ?? '-'" />
                <x-ui.description-item label="Update Terakhir" :value="$registration->updated_at?->format('d M Y H:i') ?? '-'" />
                <x-ui.description-item label="Status"><x-ui.badge :status="$registration->status">{{ $statusLabel }}</x-ui.badge></x-ui.description-item>
            </x-ui.description-list>
            <div class="mt-5">
                <h2 class="font-heading text-sm font-bold text-etc-on-surface">Catatan Internal</h2>
                <p class="mt-2 whitespace-pre-line text-sm leading-6 text-etc-on-muted">{{ $registration->notes ?: 'Tidak ada catatan.' }}</p>
            </div>
        </x-ui.detail-card>
    </div>
</x-layouts.dashboard>
