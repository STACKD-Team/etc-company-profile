<x-layouts.dashboard :title="$title" :area="$area ?? 'admin'" :active="$active ?? null">
    @if (session('status'))
        <x-ui.alert status="success" class="mb-5">{{ session('status') }}</x-ui.alert>
    @endif

    <x-ui.resource-header
        :title="$heading"
        :subtitle="$description"
        :back-url="route('admin.placement-test.index')"
    >
        @if (isset($details['Status']))
            <x-slot:status>
                <x-ui.badge :status="str($details['Status'])->lower()->replace(' ', '_')->toString()">{{ $details['Status'] }}</x-ui.badge>
            </x-slot:status>
        @endif
    </x-ui.resource-header>

    <x-ui.detail-card heading="Detail Placement Test">
        <x-ui.description-list>
            @foreach ($details as $label => $value)
                <x-ui.description-item :label="$label" :value="$value" />
            @endforeach
        </x-ui.description-list>
    </x-ui.detail-card>

    @isset($placementRegistration)
        <div class="mt-6 grid gap-5 xl:grid-cols-[0.9fr_1.1fr]">
            <x-ui.detail-card heading="Jadwal Placement Test" description="Placement tetap luring; sistem menyimpan jadwal agar alur intake terpantau." icon="heroicon-o-calendar-days">
                <form method="POST" action="{{ $placementScheduleAction }}" class="space-y-5">
                    @csrf
                    <x-ui.date-time-picker name="placement_test_at" label="Tanggal dan jam" :value="$placementRegistration->placement_test_at?->format('Y-m-d\TH:i')" required />
                    <x-ui.button type="submit" icon="heroicon-m-check">Simpan Jadwal</x-ui.button>
                </form>
            </x-ui.detail-card>

            <x-ui.detail-card heading="Hasil dan Rekomendasi Kelas" description="Simpan catatan hasil test. Pilih kelas jika siswa sudah siap diassign." icon="heroicon-o-clipboard-document-check">
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
                    <x-ui.action-bar align="start">
                        <x-ui.button type="submit" icon="heroicon-m-check">Simpan Hasil</x-ui.button>
                        <x-ui.button type="button" color="danger" outlined icon="heroicon-m-trash" data-open-modal="clear-placement-modal">
                            Clear
                        </x-ui.button>
                    </x-ui.action-bar>
                </form>
            </x-ui.detail-card>
        </div>

        <x-ui.modal id="clear-placement-modal" heading="Clear placement test?" description="Jadwal, hasil, dan rekomendasi kelas akan dikosongkan. Status kembali ke paid." icon="heroicon-o-trash" icon-color="danger">
            <form method="POST" action="{{ route('admin.placement-test.clear', $placementRegistration) }}">
                @csrf
                @method('DELETE')
                <input type="hidden" name="confirm" value="1">
                <x-ui.button type="submit" color="danger" icon="heroicon-m-trash">Clear Placement</x-ui.button>
            </form>
        </x-ui.modal>
    @endisset
</x-layouts.dashboard>
