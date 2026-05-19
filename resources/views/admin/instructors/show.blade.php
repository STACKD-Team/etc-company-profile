<x-layouts.dashboard title="Detail Instruktur" area="admin" active="instructors">
    <section class="rounded-card bg-white p-6 shadow-panel">
        <h2 class="font-heading text-2xl font-black">{{ $instructor->full_name ?? $instructor->name }}</h2>
        <p class="mt-2 text-sm text-etc-on-muted">{{ $instructor->email }} • {{ $instructor->instructor_specialization ?? '-' }}</p>
        <div class="mt-6 rounded-card bg-etc-surface-low p-4">
            <strong>Bio</strong>
            <p class="mt-2 text-sm leading-6">{{ $instructor->instructor_bio ?? 'Belum ada bio.' }}</p>
        </div>
    </section>
</x-layouts.dashboard>
