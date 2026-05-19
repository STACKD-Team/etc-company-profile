<x-layouts.dashboard title="Master Kelas" area="admin" active="classes">
    <section class="rounded-card bg-white p-6 shadow-panel">
        @if (session('status'))<div class="mb-4 rounded-card bg-green-50 p-3 text-sm font-bold text-green-700">{{ session('status') }}</div>@endif
        <div class="mb-5 flex justify-end"><a href="{{ route('admin.classes.create') }}" class="rounded-pill bg-etc-magenta px-5 py-3 text-sm font-bold text-white">Tambah Kelas</a></div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="text-etc-on-muted"><tr><th class="py-3">Kelas</th><th>Program</th><th>Instruktur</th><th>Status</th><th></th></tr></thead>
                <tbody>
                    @foreach ($classes as $class)
                        <tr class="border-t border-etc-outline-variant/60">
                            <td class="py-3 font-bold">{{ $class->name }}</td>
                            <td>{{ $class->program?->name }}</td>
                            <td>{{ $class->instructor?->full_name ?? $class->instructor?->name ?? '-' }}</td>
                            <td>{{ $class->status }}</td>
                            <td class="text-right"><a class="font-bold text-etc-magenta" href="{{ route('admin.classes.edit', $class) }}">Edit</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-5">{{ $classes->links() }}</div>
    </section>
</x-layouts.dashboard>
