@props([
    'action',
    'label' => 'Hapus',
    'heading' => 'Hapus data?',
    'description' => 'Data akan dihapus dari daftar aktif. Tindakan ini memakai soft delete bila resource mendukung.',
    'triggerSize' => 'md',
])

@php($modalId = 'delete-action-'.substr(md5($action.$heading), 0, 10))

<x-ui.modal :id="$modalId" :heading="$heading" :description="$description" icon="heroicon-o-trash" icon-color="danger">
    <x-slot:trigger>
        <x-ui.button type="button" color="danger" outlined :size="$triggerSize" icon="heroicon-m-trash">
            {{ $label }}
        </x-ui.button>
    </x-slot:trigger>

    <p class="text-sm text-etc-on-muted">
        Pastikan data ini memang tidak lagi dipakai pada operasional aktif.
    </p>

    <x-slot:footer>
        <form method="POST" action="{{ $action }}">
            @csrf
            @method('DELETE')
            <input type="hidden" name="confirm" value="1">
            <x-ui.button type="submit" color="danger" icon="heroicon-m-trash">
                {{ $label }}
            </x-ui.button>
        </form>
    </x-slot:footer>
</x-ui.modal>
