@php
    $statusLabels = [
        'pending_payment' => 'Menunggu Pembayaran',
        'paid' => 'Paid',
        'placement_test' => 'Placement Test',
        'enrolled' => 'Enrolled',
        'rejected' => 'Rejected',
        'cancelled' => 'Cancelled',
    ];
@endphp

<x-layouts.dashboard title="Data Pendaftaran" area="admin" active="registrations">
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
            <select name="program_id" class="min-h-11 rounded-xl border border-etc-outline-variant px-4 text-sm">
                <option value="">Semua program</option>
                @foreach ($programs as $program)
                    <option value="{{ $program->id }}" @selected((string) request('program_id') === (string) $program->id)>{{ $program->name }}</option>
                @endforeach
            </select>
            <input name="created_from" type="date" value="{{ request('created_from') }}" class="min-h-11 rounded-xl border border-etc-outline-variant px-4 text-sm">
            <button class="min-h-11 rounded-pill bg-etc-magenta px-5 font-heading text-sm font-bold text-white">Filter</button>
        </form>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="font-heading text-xs uppercase text-etc-on-muted">
                    <tr>
                        <th class="py-3 pr-4">Kode</th>
                        <th class="py-3 pr-4">Pendaftar</th>
                        <th class="py-3 pr-4">Program</th>
                        <th class="py-3 pr-4">Jadwal</th>
                        <th class="py-3 pr-4">Status</th>
                        <th class="py-3 pr-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($registrations as $registration)
                        <tr class="border-t border-etc-outline-variant/60">
                            <td class="py-4 pr-4 font-heading font-bold">{{ $registration->registration_code }}</td>
                            <td class="py-4 pr-4">
                                <p class="font-bold">{{ $registration->applicant_name }}</p>
                                <p class="text-xs text-etc-on-muted">{{ $registration->applicant_email }}</p>
                            </td>
                            <td class="py-4 pr-4">{{ $registration->program?->name ?? '-' }}</td>
                            <td class="py-4 pr-4">{{ $registration->preferred_days ?? '-' }}<br><span class="text-xs text-etc-on-muted">{{ $registration->preferred_time ?? '-' }}</span></td>
                            <td class="py-4 pr-4"><span class="rounded-pill bg-etc-surface-container px-3 py-1 text-xs font-bold text-etc-on-surface">{{ $statusLabels[$registration->status] ?? $registration->status }}</span></td>
                            <td class="py-4 pr-4">
                                <div class="flex gap-3 font-heading text-sm font-bold">
                                    <a href="{{ route('admin.registrations.show', $registration) }}" class="text-etc-magenta">Detail</a>
                                    <a href="{{ route('admin.registrations.edit', $registration) }}" class="text-etc-on-muted">Edit</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="py-8 text-center text-etc-on-muted">Belum ada pendaftaran sesuai filter.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-5">{{ $registrations->links() }}</div>
    </section>
</x-layouts.dashboard>
