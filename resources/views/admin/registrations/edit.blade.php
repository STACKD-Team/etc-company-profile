@php
    $statuses = ['pending_payment', 'paid', 'placement_test', 'enrolled', 'rejected', 'cancelled'];
    $days = ['mon_wed' => 'Mon-Wed', 'tue_thu' => 'Tues-Thurs', 'wed_fri' => 'Wed-Fri', 'sat_sun' => 'Sat-Sun', 'request' => 'Request Schedule'];
    $methods = ['qris' => 'QRIS', 'bank_transfer' => 'Transfer Bank', 'virtual_account' => 'Virtual Account', 'ewallet' => 'E-Wallet', 'manual' => 'Manual'];
@endphp

<x-layouts.dashboard title="Edit Pendaftaran" area="admin" active="registrations">
    <section class="rounded-card bg-white p-6 shadow-panel">
        <form method="POST" action="{{ route('admin.registrations.update', $registration) }}" class="grid gap-5 md:grid-cols-2">
            @csrf
            @method('PUT')

            <label class="space-y-2">
                <span class="font-heading text-sm font-bold">Nama Pendaftar</span>
                <input name="applicant_name" value="{{ old('applicant_name', $registration->applicant_name) }}" class="min-h-11 w-full rounded-xl border border-etc-outline-variant px-4 text-sm">
                @error('applicant_name')<span class="text-xs font-bold text-red-600">{{ $message }}</span>@enderror
            </label>

            <label class="space-y-2">
                <span class="font-heading text-sm font-bold">Email</span>
                <input name="applicant_email" type="email" value="{{ old('applicant_email', $registration->applicant_email) }}" class="min-h-11 w-full rounded-xl border border-etc-outline-variant px-4 text-sm">
                @error('applicant_email')<span class="text-xs font-bold text-red-600">{{ $message }}</span>@enderror
            </label>

            <label class="space-y-2">
                <span class="font-heading text-sm font-bold">No HP</span>
                <input name="applicant_phone" value="{{ old('applicant_phone', $registration->applicant_phone) }}" class="min-h-11 w-full rounded-xl border border-etc-outline-variant px-4 text-sm">
                @error('applicant_phone')<span class="text-xs font-bold text-red-600">{{ $message }}</span>@enderror
            </label>

            <label class="space-y-2">
                <span class="font-heading text-sm font-bold">Program</span>
                <select name="program_id" class="min-h-11 w-full rounded-xl border border-etc-outline-variant px-4 text-sm">
                    @foreach ($programs as $program)
                        <option value="{{ $program->id }}" @selected((string) old('program_id', $registration->program_id) === (string) $program->id)>{{ $program->name }}</option>
                    @endforeach
                </select>
                @error('program_id')<span class="text-xs font-bold text-red-600">{{ $message }}</span>@enderror
            </label>

            <label class="space-y-2">
                <span class="font-heading text-sm font-bold">Preferensi Hari</span>
                <select name="preferred_days" class="min-h-11 w-full rounded-xl border border-etc-outline-variant px-4 text-sm">
                    <option value="">Belum dipilih</option>
                    @foreach ($days as $value => $label)
                        <option value="{{ $value }}" @selected(old('preferred_days', $registration->preferred_days) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('preferred_days')<span class="text-xs font-bold text-red-600">{{ $message }}</span>@enderror
            </label>

            <label class="space-y-2">
                <span class="font-heading text-sm font-bold">Preferensi Jam</span>
                <input name="preferred_time" value="{{ old('preferred_time', $registration->preferred_time) }}" class="min-h-11 w-full rounded-xl border border-etc-outline-variant px-4 text-sm">
                @error('preferred_time')<span class="text-xs font-bold text-red-600">{{ $message }}</span>@enderror
            </label>

            <label class="space-y-2">
                <span class="font-heading text-sm font-bold">Metode Pembayaran</span>
                <select name="payment_method" class="min-h-11 w-full rounded-xl border border-etc-outline-variant px-4 text-sm">
                    <option value="">Belum dikonfirmasi</option>
                    @foreach ($methods as $value => $label)
                        <option value="{{ $value }}" @selected(old('payment_method', $registration->payment_method) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('payment_method')<span class="text-xs font-bold text-red-600">{{ $message }}</span>@enderror
            </label>

            <label class="space-y-2">
                <span class="font-heading text-sm font-bold">Nominal Pembayaran</span>
                <input name="payment_amount" type="number" min="0" value="{{ old('payment_amount', $registration->payment_amount) }}" class="min-h-11 w-full rounded-xl border border-etc-outline-variant px-4 text-sm">
                @error('payment_amount')<span class="text-xs font-bold text-red-600">{{ $message }}</span>@enderror
            </label>

            <label class="space-y-2">
                <span class="font-heading text-sm font-bold">Status</span>
                <select name="status" class="min-h-11 w-full rounded-xl border border-etc-outline-variant px-4 text-sm">
                    @foreach ($statuses as $status)
                        <option value="{{ $status }}" @selected(old('status', $registration->status) === $status)>{{ str($status)->replace('_', ' ')->headline() }}</option>
                    @endforeach
                </select>
                @error('status')<span class="text-xs font-bold text-red-600">{{ $message }}</span>@enderror
            </label>

            <label class="space-y-2 md:col-span-2">
                <span class="font-heading text-sm font-bold">Catatan</span>
                <textarea name="notes" rows="4" class="w-full rounded-xl border border-etc-outline-variant px-4 py-3 text-sm">{{ old('notes', $registration->notes) }}</textarea>
                @error('notes')<span class="text-xs font-bold text-red-600">{{ $message }}</span>@enderror
            </label>

            <div class="flex flex-wrap gap-3 md:col-span-2">
                <button class="rounded-pill bg-etc-magenta px-6 py-3 font-heading text-sm font-bold text-white">Simpan</button>
                <a href="{{ route('admin.registrations.show', $registration) }}" class="rounded-pill border border-etc-outline-variant px-6 py-3 font-heading text-sm font-bold text-etc-on-surface">Batal</a>
            </div>
        </form>
    </section>
</x-layouts.dashboard>
