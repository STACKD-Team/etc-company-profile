<x-layouts.dashboard :title="$program->name" area="admin" active="programs">
    @if (session('status'))
        <x-ui.alert status="success" class="mb-5">{{ session('status') }}</x-ui.alert>
    @endif

    <x-ui.resource-header
        :title="$program->name"
        :subtitle="$program->description ?: 'Detail program, kelas, pendaftaran, dan promo terkait.'"
        :back-url="route('admin.program.index')"
    >
        <x-slot:status>
            <x-ui.badge :status="$program->is_active ? 'active' : 'inactive'">{{ $program->is_active ? 'Aktif' : 'Nonaktif' }}</x-ui.badge>
        </x-slot:status>
        <x-slot:actions>
            <x-ui.button :href="route('admin.program.edit', $program)" icon="heroicon-m-pencil-square">Edit</x-ui.button>
        </x-slot:actions>
    </x-ui.resource-header>

    <div class="grid gap-4 md:grid-cols-3">
        <x-ui.stat-card label="Kelas" :value="$program->classes->count()" icon="heroicon-m-academic-cap" />
        <x-ui.stat-card label="Pendaftaran" :value="$program->registrations->count()" icon="heroicon-m-clipboard-document-list" />
        <x-ui.stat-card label="Promo Aktif" :value="$program->activePromotions()->count()" icon="heroicon-m-tag" />
    </div>

    <x-ui.detail-card heading="Detail Program" class="mt-6">
        <x-ui.description-list columns="3">
            <x-ui.description-item label="Slug" :value="$program->slug" />
            <x-ui.description-item label="Kategori" :value="str($program->category)->replace('_', ' ')->headline()" />
            <x-ui.description-item label="Tipe" :value="str($program->type)->replace('_', ' ')->headline()" />
            <x-ui.description-item label="Target Usia" :value="str($program->target_age)->headline()" />
            <x-ui.description-item label="Durasi" :value="$program->duration_meetings ? $program->duration_meetings.' pertemuan' : '-'" />
            <x-ui.description-item label="Maks. Siswa" :value="$program->max_students ?: '-'" />
            <x-ui.description-item label="Biaya Program" :value="'Rp '.number_format((float) $program->price, 0, ',', '.')" />
            <x-ui.description-item label="Biaya Pendaftaran" :value="'Rp '.number_format((float) $program->registration_fee, 0, ',', '.')" />
            <x-ui.description-item label="Status">
                <x-ui.badge :status="$program->is_active ? 'active' : 'inactive'">{{ $program->is_active ? 'Aktif' : 'Nonaktif' }}</x-ui.badge>
            </x-ui.description-item>
        </x-ui.description-list>
    </x-ui.detail-card>

    <div class="mt-6 grid gap-6 xl:grid-cols-2">
        <x-ui.detail-card heading="Kelas Terkait">
            <x-ui.data-table
                :items="$program->classes"
                :columns="[
                    'name' => 'Kelas',
                    'instructor' => 'Instructor',
                    'room' => 'Room',
                    'status' => 'Status',
                    'actions' => 'Aksi',
                ]"
                row-view="pages.admin.program.partials.class-row"
                empty="Belum ada kelas"
                empty-description="Kelas program ini akan tampil setelah dibuat."
                :show-search="false"
            />
        </x-ui.detail-card>

        <x-ui.detail-card heading="Pendaftaran Terkait">
            <x-ui.data-table
                :items="$program->registrations"
                :columns="[
                    'code' => 'Kode',
                    'name' => 'Nama',
                    'payment' => 'Payment',
                    'status' => 'Status',
                    'actions' => 'Aksi',
                ]"
                row-view="pages.admin.program.partials.registration-row"
                empty="Belum ada pendaftaran"
                empty-description="Pendaftaran untuk program ini akan tampil di sini."
                :show-search="false"
            />
        </x-ui.detail-card>
    </div>

    <x-ui.detail-card heading="Promo Program" class="mt-6">
        <x-ui.data-table
            :items="$program->promotions"
            :columns="[
                'title' => 'Promo',
                'discount' => 'Diskon',
                'period' => 'Periode',
                'status' => 'Status',
            ]"
            row-view="pages.admin.program.partials.promotion-row"
            empty="Belum ada promo"
            empty-description="Promo program akan tampil setelah tersedia."
            :show-search="false"
        />
    </x-ui.detail-card>
</x-layouts.dashboard>
