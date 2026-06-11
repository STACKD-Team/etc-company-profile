@csrf
@if ($class->exists) @method('PUT') @endif

@php
    $programOptions = $programs->pluck('name', 'id')->all();
    $instructorOptions = $instructors->mapWithKeys(fn ($instructor) => [$instructor->id => $instructor->full_name ?? $instructor->name])->all();
    $statuses = ['upcoming' => 'Upcoming', 'ongoing' => 'Ongoing', 'completed' => 'Completed', 'cancelled' => 'Cancelled'];
    $dateValue = static function ($value): ?string {
        return $value instanceof \Carbon\CarbonInterface ? $value->format('Y-m-d') : $value;
    };
@endphp

<div class="grid gap-5 md:grid-cols-2">
    <x-ui.select name="program_id" label="Program" :value="$class->program_id" :options="$programOptions" required />
    <x-ui.select name="instructor_id" label="Instruktur" :value="$class->instructor_id" placeholder="Belum ditentukan" :options="$instructorOptions" />
    <x-ui.field name="name" label="Nama Kelas" :value="$class->name" required />
    <x-ui.field name="schedule_days" label="Hari" :value="$class->schedule_days" />
    <x-ui.field name="schedule_time" label="Jam" :value="$class->schedule_time" />
    <x-ui.field name="room" label="Ruangan" :value="$class->room" />
    <x-ui.date-picker name="start_date" label="Tanggal Mulai" :value="$dateValue($class->start_date)" />
    <x-ui.date-picker name="end_date" label="Tanggal Selesai" :value="$dateValue($class->end_date)" />
    <x-ui.select name="status" label="Status" :value="$class->status ?: 'upcoming'" :options="$statuses" required />
</div>
<div class="mt-6 flex flex-wrap gap-3">
    <x-ui.button type="submit" icon="heroicon-m-check">Simpan</x-ui.button>
    <x-ui.button :href="route('admin.classes.index')" outlined color="gray">Batal</x-ui.button>
</div>
