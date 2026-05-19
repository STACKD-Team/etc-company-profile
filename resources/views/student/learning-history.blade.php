<x-layouts.dashboard title="Riwayat Pembelajaran" area="student" active="classes" :user="$student">
    <div class="rounded-card bg-white p-6 shadow-panel">
        <div class="space-y-4">
            @forelse ($enrollments as $enrollment)
                <div class="flex items-center justify-between border-b border-etc-outline-variant/60 pb-4">
                    <div>
                        <strong class="font-heading">{{ $enrollment->courseClass?->program?->name }} - {{ $enrollment->courseClass?->name }}</strong>
                        <p class="mt-1 text-sm text-etc-on-muted">{{ $enrollment->enrolled_at?->format('d M Y') }} - {{ $enrollment->completed_at?->format('d M Y') ?? 'Berjalan' }}</p>
                    </div>
                    <span class="rounded-full bg-etc-surface-low px-3 py-1 text-xs font-bold text-etc-magenta">{{ $enrollment->status }}</span>
                </div>
            @empty
                <p class="text-center text-sm text-etc-on-muted">Belum ada riwayat pembelajaran.</p>
            @endforelse
        </div>
    </div>
</x-layouts.dashboard>
