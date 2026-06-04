<x-layouts.dashboard title="Data Enrollment" area="admin" active="enrollments">
    <section class="rounded-card bg-white p-6 shadow-panel">
        @if (session('status'))<div class="mb-4 rounded-card bg-green-50 p-3 text-sm font-bold text-green-700">{{ session('status') }}</div>@endif
        <form method="POST" action="{{ route('admin.enrollments.store') }}" class="mb-8 grid gap-4 rounded-card bg-etc-surface-low p-4 md:grid-cols-5">
            @csrf
            <select name="user_id" class="rounded-xl border border-etc-outline-variant px-4 py-3 text-sm"><option value="">Pilih siswa</option>@foreach ($students as $student)<option value="{{ $student->id }}" @selected(old('user_id') == $student->id)>{{ $student->full_name ?? $student->name }}</option>@endforeach</select>
            <select name="class_id" class="rounded-xl border border-etc-outline-variant px-4 py-3 text-sm"><option value="">Pilih kelas</option>@foreach ($classes as $class)<option value="{{ $class->id }}" @selected(old('class_id') == $class->id)>{{ $class->program?->name }} - {{ $class->name }}</option>@endforeach</select>
            <input name="enrolled_at" type="date" value="{{ old('enrolled_at', now()->toDateString()) }}" class="rounded-xl border border-etc-outline-variant px-4 py-3 text-sm">
            <select name="status" class="rounded-xl border border-etc-outline-variant px-4 py-3 text-sm">
                @foreach (['active' => 'Aktif', 'completed' => 'Selesai', 'dropped' => 'Berhenti'] as $value => $label)
                    <option value="{{ $value }}" @selected(old('status', 'active') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <button class="rounded-pill bg-etc-magenta px-5 text-sm font-bold text-white">Assign</button>
            @if ($errors->any())<p class="text-sm text-red-600 md:col-span-5">{{ $errors->first() }}</p>@endif
        </form>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="text-etc-on-muted"><tr><th class="py-3">Siswa</th><th>Kelas</th><th>Tanggal</th><th>Status</th></tr></thead>
                <tbody>
                    @foreach ($enrollments as $enrollment)
                        <tr class="border-t border-etc-outline-variant/60">
                            <td class="py-3 font-bold">{{ $enrollment->user?->full_name ?? $enrollment->user?->name }}</td>
                            <td>{{ $enrollment->courseClass?->program?->name }} - {{ $enrollment->courseClass?->name }}</td>
                            <td>{{ $enrollment->enrolled_at?->format('d M Y') }}</td>
                            <td>{{ $enrollment->status }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-5">{{ $enrollments->links() }}</div>
    </section>
</x-layouts.dashboard>
