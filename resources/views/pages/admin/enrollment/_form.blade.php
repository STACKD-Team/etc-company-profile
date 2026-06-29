@csrf
@if ($enrollment->exists) @method('PUT') @endif

@php
    $statuses = ['active' => 'Aktif', 'completed' => 'Selesai', 'dropped' => 'Berhenti'];
    $studentOptions = $students->mapWithKeys(fn ($student) => [$student->id => $student->full_name ?? $student->name])->all();
    $classOptions = $classes->mapWithKeys(fn ($class) => [$class->id => trim(($class->program?->name ?? '-').' - '.$class->name)])->all();
@endphp

<div class="grid gap-5 md:grid-cols-2">
    <x-ui.select name="user_id" label="Siswa" placeholder="Pilih siswa" :value="$enrollment->user_id" :options="$studentOptions" required />
    <x-ui.select name="class_id" label="Kelas" placeholder="Pilih kelas" :value="$enrollment->class_id" :options="$classOptions" required />
    <x-ui.date-picker name="enrolled_at" label="Tanggal Masuk" :value="old('enrolled_at', $enrollment->enrolled_at?->format('Y-m-d') ?? now()->toDateString())" required />
    <x-ui.date-picker name="completed_at" label="Tanggal Selesai" :value="$enrollment->completed_at?->format('Y-m-d')" />
    <x-ui.select name="status" label="Status" :value="$enrollment->status ?: 'active'" :options="$statuses" required />
</div>
