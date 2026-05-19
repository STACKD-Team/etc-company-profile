<x-layouts.dashboard title="Data Siswa" area="admin" active="students">
    <section class="rounded-card bg-white p-6 shadow-panel">
        <form method="GET" class="mb-5 flex gap-3">
            <input name="search" value="{{ request('search') }}" placeholder="Cari siswa" class="min-h-11 flex-1 rounded-xl border border-etc-outline-variant px-4 text-sm">
            <button class="rounded-pill bg-etc-magenta px-5 font-heading text-sm font-bold text-white">Cari</button>
        </form>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="text-etc-on-muted"><tr><th class="py-3">Nama</th><th>Email</th><th>Kelas</th><th></th></tr></thead>
                <tbody>
                    @foreach ($students as $student)
                        <tr class="border-t border-etc-outline-variant/60">
                            <td class="py-3 font-bold">{{ $student->full_name ?? $student->name }}</td>
                            <td>{{ $student->email }}</td>
                            <td>{{ $student->enrollments_count }}</td>
                            <td class="text-right"><a class="font-bold text-etc-magenta" href="{{ route('admin.students.show', $student) }}">Detail</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-5">{{ $students->links() }}</div>
    </section>
</x-layouts.dashboard>
