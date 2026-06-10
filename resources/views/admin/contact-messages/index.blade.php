<x-layouts.dashboard title="Pesan Kontak" area="admin" active="contact_messages">
    <x-ui.data-table
        :items="$messages"
        :columns="[
            'name' => ['label' => 'Pengirim', 'sortable' => true],
            'subject' => ['label' => 'Subjek', 'sortable' => true],
            'is_read' => ['label' => 'Status', 'sortable' => true, 'filter' => ['type' => 'select', 'name' => 'is_read', 'options' => ['0' => 'Belum dibaca', '1' => 'Sudah dibaca']]],
            'created_at' => ['label' => 'Tanggal', 'sortable' => true],
            'actions' => 'Aksi',
        ]"
        row-view="admin.contact-messages.partials.row"
        empty="Tidak ada pesan"
        empty-description="Pesan kontak dari public website akan tampil di sini."
        search-placeholder="Cari nama, email, atau subjek"
    />
</x-layouts.dashboard>
