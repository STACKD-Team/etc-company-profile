@csrf
@if ($registration->exists) @method('PUT') @endif

@php
    $statuses = collect(['pending_payment', 'paid', 'placement_test', 'enrolled', 'rejected', 'cancelled'])
        ->mapWithKeys(fn ($status) => [$status => str($status)->replace('_', ' ')->headline()->toString()])
        ->all();
    $days = ['mon_wed' => 'Mon-Wed', 'tue_thu' => 'Tues-Thurs', 'wed_fri' => 'Wed-Fri', 'sat_sun' => 'Sat-Sun', 'request' => 'Request Schedule'];
    $methods = ['qris' => 'QRIS', 'bank_transfer' => 'Transfer Bank', 'virtual_account' => 'Virtual Account', 'ewallet' => 'E-Wallet', 'manual' => 'Manual'];
@endphp

<x-ui.field name="applicant_name" label="Nama Pendaftar" :value="$registration->applicant_name" required />
<x-ui.email-field name="applicant_email" label="Email" :value="$registration->applicant_email" required />
<x-ui.phone-field name="applicant_phone" label="No HP" :value="$registration->applicant_phone" required />
<x-ui.select name="program_id" label="Program" :value="$registration->program_id" :options="$programs->pluck('name', 'id')->all()" required />
<x-ui.select name="preferred_days" label="Preferensi Hari" :value="$registration->preferred_days" placeholder="Belum dipilih" :options="$days" />
<x-ui.field name="preferred_time" label="Preferensi Jam" :value="$registration->preferred_time" />
<x-ui.select name="payment_method" label="Metode Pembayaran" :value="$registration->payment_method" placeholder="Belum dikonfirmasi" :options="$methods" />
<x-ui.currency-field name="payment_amount" label="Nominal Pembayaran" :value="$registration->payment_amount" />
<x-ui.select name="status" label="Status" :value="$registration->status ?: 'pending_payment'" :options="$statuses" required />

<div class="md:col-span-2">
    <x-ui.textarea name="notes" label="Catatan" rows="4" :value="$registration->notes" />
</div>
