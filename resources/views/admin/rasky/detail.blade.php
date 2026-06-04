<x-layouts.dashboard :title="$title" :area="$area ?? 'admin'" :active="$active ?? null">
    @if (session('status'))
        <div class="mb-5 rounded-lg bg-etc-surface-container p-4 text-sm text-etc-on-surface">{{ session('status') }}</div>
    @endif

    <section class="rounded-card bg-white p-6 shadow-soft">
        <h2 class="font-heading text-2xl font-black text-etc-on-surface">{{ $heading }}</h2>
        <p class="mt-2 text-sm text-etc-on-muted">{{ $description }}</p>

        <dl class="mt-6 grid gap-4 md:grid-cols-2">
            @foreach ($details as $label => $value)
                <div class="rounded-lg bg-etc-surface p-4">
                    <dt class="font-heading text-xs font-bold uppercase text-etc-on-muted">{{ $label }}</dt>
                    <dd class="mt-2 text-sm text-etc-on-surface">{{ $value }}</dd>
                </div>
            @endforeach
        </dl>
    </section>

    @isset($placementRegistration)
        <section class="mt-6 grid gap-5 xl:grid-cols-[0.9fr_1.1fr]">
            <form method="POST" action="{{ $placementScheduleAction }}" class="rounded-card border border-etc-outline-variant/60 bg-white p-6 shadow-soft">
                @csrf
                <div class="flex items-start gap-3">
                    <span class="material-symbols-outlined rounded-full bg-etc-surface-container p-3 text-etc-magenta">event</span>
                    <div>
                        <h3 class="font-heading text-lg font-black text-etc-on-surface">Jadwal Placement Test</h3>
                        <p class="mt-1 text-sm text-etc-on-muted">Placement tetap luring; sistem menyimpan jadwal agar alur intake terpantau.</p>
                    </div>
                </div>

                <label for="placement_test_at" class="mt-6 block font-heading text-sm font-bold text-etc-on-surface">Tanggal dan jam</label>
                <input
                    id="placement_test_at"
                    name="placement_test_at"
                    type="datetime-local"
                    value="{{ old('placement_test_at', $placementRegistration->placement_test_at?->format('Y-m-d\TH:i')) }}"
                    class="mt-2 min-h-12 w-full rounded-lg border border-etc-outline-variant bg-white px-4 py-3 text-sm text-etc-on-surface outline-none transition focus:border-etc-magenta focus:ring-2 focus:ring-etc-magenta/15"
                >
                @error('placement_test_at')
                    <p class="mt-2 text-sm text-red-700">{{ $message }}</p>
                @enderror

                <button type="submit" class="mt-5 inline-flex min-h-12 items-center justify-center gap-2 rounded-full bg-etc-magenta px-5 py-3 font-heading text-sm font-bold text-white transition hover:bg-etc-primary">
                    <span class="material-symbols-outlined text-lg">save</span>
                    Simpan Jadwal
                </button>
            </form>

            <form method="POST" action="{{ $placementResultAction }}" class="rounded-card border border-etc-outline-variant/60 bg-white p-6 shadow-soft">
                @csrf
                <div class="flex items-start gap-3">
                    <span class="material-symbols-outlined rounded-full bg-etc-surface-container p-3 text-etc-magenta">assignment_turned_in</span>
                    <div>
                        <h3 class="font-heading text-lg font-black text-etc-on-surface">Hasil dan Rekomendasi Kelas</h3>
                        <p class="mt-1 text-sm text-etc-on-muted">Simpan catatan hasil test. Pilih kelas jika siswa sudah siap diassign.</p>
                    </div>
                </div>

                <label for="placement_test_result" class="mt-6 block font-heading text-sm font-bold text-etc-on-surface">Hasil placement</label>
                <textarea
                    id="placement_test_result"
                    name="placement_test_result"
                    rows="5"
                    class="mt-2 w-full rounded-lg border border-etc-outline-variant bg-white px-4 py-3 text-sm text-etc-on-surface outline-none transition focus:border-etc-magenta focus:ring-2 focus:ring-etc-magenta/15"
                >{{ old('placement_test_result', $placementRegistration->placement_test_result) }}</textarea>
                @error('placement_test_result')
                    <p class="mt-2 text-sm text-red-700">{{ $message }}</p>
                @enderror

                <label for="class_id" class="mt-5 block font-heading text-sm font-bold text-etc-on-surface">Kelas rekomendasi</label>
                <select
                    id="class_id"
                    name="class_id"
                    class="mt-2 min-h-12 w-full rounded-lg border border-etc-outline-variant bg-white px-4 py-3 text-sm text-etc-on-surface outline-none transition focus:border-etc-magenta focus:ring-2 focus:ring-etc-magenta/15"
                >
                    <option value="">Belum assign kelas</option>
                    @foreach ($placementClasses as $class)
                        <option value="{{ $class->id }}" @selected((string) old('class_id', $placementRegistration->class_id) === (string) $class->id)>
                            {{ $class->name }} @if ($class->status) - {{ str($class->status)->headline() }} @endif
                        </option>
                    @endforeach
                </select>
                @error('class_id')
                    <p class="mt-2 text-sm text-red-700">{{ $message }}</p>
                @enderror

                <button type="submit" class="mt-5 inline-flex min-h-12 items-center justify-center gap-2 rounded-full bg-etc-magenta px-5 py-3 font-heading text-sm font-bold text-white transition hover:bg-etc-primary">
                    <span class="material-symbols-outlined text-lg">done</span>
                    Simpan Hasil
                </button>
            </form>
        </section>
    @endisset
</x-layouts.dashboard>
