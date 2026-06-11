<x-layouts.dashboard :title="$title" :area="$area ?? 'admin'" :active="$active ?? null">
    @if (session('status'))
        <x-ui.alert status="success" class="mb-5">{{ session('status') }}</x-ui.alert>
    @endif

    <x-ui.panel>
        <div class="flex flex-col justify-between gap-4 md:flex-row md:items-start">
            <div>
                <p class="font-heading text-xs font-bold uppercase text-etc-magenta">{{ $title }}</p>
                <h2 class="mt-2 font-heading text-2xl font-black text-etc-on-surface">{{ $heading }}</h2>
                <p class="mt-2 text-sm text-etc-on-muted">{{ $description }}</p>
            </div>
            @if (isset($details['Status']))
                <x-ui.badge :status="str($details['Status'])->lower()->replace(' ', '_')->toString()">{{ $details['Status'] }}</x-ui.badge>
            @endif
        </div>

        <dl class="mt-6 divide-y-2 divide-etc-outline-variant/60 border-t-2 border-etc-outline-variant/60">
            @foreach ($details as $label => $value)
                <div class="grid gap-1 py-3 md:grid-cols-[160px_1fr] md:gap-4">
                    <dt class="font-heading text-xs font-bold uppercase text-etc-on-muted">{{ $label }}</dt>
                    <dd class="text-sm text-etc-on-surface">{{ $value }}</dd>
                </div>
            @endforeach
        </dl>
    </x-ui.panel>

    @isset($placementRegistration)
        <div class="mt-6 grid gap-5 xl:grid-cols-[0.9fr_1.1fr]">
            <x-ui.panel heading="Jadwal Placement Test" description="Placement tetap luring; sistem menyimpan jadwal agar alur intake terpantau." icon="heroicon-o-calendar-days">
                <form method="POST" action="{{ $placementScheduleAction }}" class="space-y-5">
                    @csrf
                    <x-ui.date-time-picker name="placement_test_at" label="Tanggal dan jam" :value="$placementRegistration->placement_test_at?->format('Y-m-d\TH:i')" required />
                    <x-ui.button type="submit" icon="heroicon-m-check">Simpan Jadwal</x-ui.button>
                </form>
            </x-ui.panel>

            <x-ui.panel heading="Hasil dan Rekomendasi Kelas" description="Simpan catatan hasil test. Pilih kelas jika siswa sudah siap diassign." icon="heroicon-o-clipboard-document-check">
                <form method="POST" action="{{ $placementResultAction }}" class="space-y-5">
                    @csrf
                    <x-ui.textarea name="placement_test_result" label="Hasil placement" rows="5" :value="$placementRegistration->placement_test_result" required />
                    <x-ui.select
                        name="class_id"
                        label="Kelas rekomendasi"
                        :value="$placementRegistration->class_id"
                        placeholder="Belum assign kelas"
                        :options="$placementClasses->mapWithKeys(fn ($class) => [$class->id => trim($class->name.($class->status ? ' - '.str($class->status)->headline() : ''))])->all()"
                    />
                    <x-ui.button type="submit" icon="heroicon-m-check">Simpan Hasil</x-ui.button>
                </form>
            </x-ui.panel>
        </div>
    @endisset
</x-layouts.dashboard>
