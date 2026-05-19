@csrf
@if ($class->exists) @method('PUT') @endif
<div class="grid gap-5 md:grid-cols-2">
    <label><span class="font-heading text-sm font-bold">Program</span><select name="program_id" class="mt-2 w-full rounded-xl border border-etc-outline-variant px-4 py-3 text-sm">@foreach ($programs as $program)<option value="{{ $program->id }}" @selected(old('program_id', $class->program_id) == $program->id)>{{ $program->name }}</option>@endforeach</select></label>
    <label><span class="font-heading text-sm font-bold">Instruktur</span><select name="instructor_id" class="mt-2 w-full rounded-xl border border-etc-outline-variant px-4 py-3 text-sm"><option value="">Belum ditentukan</option>@foreach ($instructors as $instructor)<option value="{{ $instructor->id }}" @selected(old('instructor_id', $class->instructor_id) == $instructor->id)>{{ $instructor->full_name ?? $instructor->name }}</option>@endforeach</select></label>
    @foreach (['name' => 'Nama Kelas', 'schedule_days' => 'Hari', 'schedule_time' => 'Jam', 'room' => 'Ruangan', 'start_date' => 'Tanggal Mulai', 'end_date' => 'Tanggal Selesai'] as $field => $label)
        <label><span class="font-heading text-sm font-bold">{{ $label }}</span><input name="{{ $field }}" type="{{ str_contains($field, 'date') ? 'date' : 'text' }}" value="{{ old($field, optional($class->{$field})->format('Y-m-d') ?? $class->{$field}) }}" class="mt-2 w-full rounded-xl border border-etc-outline-variant px-4 py-3 text-sm">@error($field)<span class="text-xs text-red-600">{{ $message }}</span>@enderror</label>
    @endforeach
    <label><span class="font-heading text-sm font-bold">Status</span><select name="status" class="mt-2 w-full rounded-xl border border-etc-outline-variant px-4 py-3 text-sm">@foreach (['upcoming','ongoing','completed','cancelled'] as $option)<option value="{{ $option }}" @selected(old('status', $class->status ?: 'upcoming') === $option)>{{ $option }}</option>@endforeach</select></label>
</div>
<button class="mt-6 rounded-pill bg-etc-magenta px-5 py-3 font-heading text-sm font-bold text-white">Simpan</button>
