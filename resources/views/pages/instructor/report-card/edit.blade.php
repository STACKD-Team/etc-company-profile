@php($studentName = $enrollment->user?->full_name ?? $enrollment->user?->name ?? '-')

<x-layouts.dashboard
    title="Edit Assessment"
    :description="'Perbarui draft assessment '.$studentName.' sebelum dipublish admin.'"
    area="instructor"
    active="reports"
>
    <x-slot:eyebrow>Assessment Siswa</x-slot:eyebrow>
    <x-slot:headerActions>
        <x-ui.button :href="route('instructor.report-cards.index')" outlined icon="heroicon-m-arrow-left">
            Kembali
        </x-ui.button>
    </x-slot:headerActions>

    @include('pages.instructor.report-card.partials.form', [
        'action' => route('instructor.report-cards.update', $reportCard),
        'method' => 'PUT',
        'gradeOptions' => ['A' => 'A', 'B' => 'B', 'C' => 'C', 'D' => 'D'],
    ])
</x-layouts.dashboard>
