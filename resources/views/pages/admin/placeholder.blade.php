<x-layouts.dashboard :title="$title" area="admin">
    <x-ui.panel>
        <p class="font-heading text-sm font-bold uppercase text-etc-magenta">{{ $routeName }}</p>
        <p class="mt-3 text-etc-on-muted">Fondasi halaman admin sudah aktif. Modul CRUD dan workflow penuh akan memakai service, FormRequest, dan authorization sesuai AGENTS.md.</p>
    </x-ui.panel>
</x-layouts.dashboard>
