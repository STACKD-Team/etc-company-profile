@props([
    'area' => 'student',
    'items' => null,
    'active' => null,
])

@php
    $routeUrl = static function (?string $routeName, string $fallback = '#'): string {
        if (! $routeName || ! \Illuminate\Support\Facades\Route::has($routeName)) {
            return $fallback;
        }

        return route($routeName);
    };

    $defaultItems = [
        'admin' => [
            ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'url' => '#', 'key' => 'dashboard', 'icon' => 'dashboard'],
            ['label' => 'Pendaftaran', 'route' => 'admin.registrations.index', 'url' => '#', 'key' => 'registrations', 'icon' => 'assignment'],
            ['label' => 'Pembayaran', 'route' => 'admin.payments.index', 'url' => '#', 'key' => 'payments', 'icon' => 'payments'],
            ['label' => 'Placement', 'route' => 'admin.placement-tests.index', 'url' => '#', 'key' => 'placement', 'icon' => 'event_available'],
            ['label' => 'Siswa', 'route' => 'admin.students.index', 'url' => '#', 'key' => 'students', 'icon' => 'groups'],
            ['label' => 'Program', 'route' => 'admin.programs.index', 'url' => '#', 'key' => 'programs', 'icon' => 'school'],
            ['label' => 'Kelas', 'route' => 'admin.classes.index', 'url' => '#', 'key' => 'classes', 'icon' => 'meeting_room'],
            ['label' => 'Enrollment', 'route' => 'admin.enrollments.index', 'url' => '#', 'key' => 'enrollments', 'icon' => 'how_to_reg'],
            ['label' => 'Rapor', 'route' => 'admin.report-cards.index', 'url' => '#', 'key' => 'reports', 'icon' => 'description'],
            ['label' => 'Export Siswa', 'route' => 'admin.exports.students', 'url' => '#', 'key' => 'student_exports', 'icon' => 'table_view'],
            ['label' => 'Export Rapor', 'route' => 'admin.exports.report-cards', 'url' => '#', 'key' => 'report_exports', 'icon' => 'file_save'],
            ['label' => 'Reels', 'route' => 'admin.reels.index', 'url' => '#', 'key' => 'reels', 'icon' => 'smart_display'],
            ['label' => 'CMS Konten', 'route' => 'admin.contents.index', 'url' => '#', 'key' => 'contents', 'icon' => 'dashboard_customize'],
            ['label' => 'Pesan Kontak', 'route' => 'admin.contact-messages.index', 'url' => '#', 'key' => 'contact_messages', 'icon' => 'inbox'],
            ['label' => 'Chatbot Logs', 'route' => 'admin.chatbot-logs.index', 'url' => '#', 'key' => 'chatbot_logs', 'icon' => 'forum'],
            ['label' => 'Settings', 'route' => 'admin.settings.index', 'url' => '#', 'key' => 'settings', 'icon' => 'settings'],
        ],
        'student' => [
            ['label' => 'Dashboard', 'route' => 'student.dashboard', 'url' => '#', 'key' => 'dashboard', 'icon' => 'dashboard'],
            ['label' => 'Profil Saya', 'route' => 'student.profile.show', 'url' => '#', 'key' => 'profile', 'icon' => 'person'],
            ['label' => 'Kelas Saya', 'route' => 'student.classes.index', 'url' => '#', 'key' => 'classes', 'icon' => 'school'],
            ['label' => 'Rapor', 'route' => 'student.report-cards.index', 'url' => '#', 'key' => 'reports', 'icon' => 'grade'],
            ['label' => 'Riwayat Pembayaran', 'route' => 'student.payments.index', 'url' => '#', 'key' => 'payments', 'icon' => 'payments'],
        ],
        'instructor' => [
            ['label' => 'Dashboard', 'route' => 'instructor.dashboard', 'url' => '#', 'key' => 'dashboard', 'icon' => 'dashboard'],
            ['label' => 'Kelas Mengajar', 'route' => 'instructor.classes.index', 'url' => '#', 'key' => 'classes', 'icon' => 'school'],
            ['label' => 'Siswa', 'route' => 'instructor.students.index', 'url' => '#', 'key' => 'students', 'icon' => 'groups'],
            ['label' => 'Rapor', 'route' => 'instructor.report-cards.index', 'url' => '#', 'key' => 'reports', 'icon' => 'description'],
        ],
    ];

    $items = collect($items ?? $defaultItems[$area] ?? $defaultItems['admin'])->map(function (array $item) use ($routeUrl) {
        $item['key'] ??= str($item['label'] ?? 'item')->slug()->toString();
        $item['url'] = $routeUrl($item['route'] ?? null, $item['url'] ?? '#');
        $item['icon'] ??= 'circle';

        return $item;
    });

    $currentActive = $active ?: $items->first(function (array $item) {
        $routeName = $item['route'] ?? null;

        return $routeName
            && \Illuminate\Support\Facades\Route::has($routeName)
            && request()->routeIs($routeName);
    })['key'] ?? 'dashboard';
@endphp

<aside
    id="dashboard-sidebar"
    {{ $attributes->class('fixed inset-y-0 left-0 z-50 flex w-72 flex-shrink-0 -translate-x-full flex-col overflow-y-auto border-r-2 border-etc-outline-variant bg-etc-surface px-3 py-4 text-etc-on-surface shadow-soft transition-[width,transform,padding] duration-300 md:relative md:z-auto md:translate-x-0') }}
    x-bind:class="{
        'translate-x-0': sidebarMobileOpen,
        '-translate-x-full': ! sidebarMobileOpen,
        'md:w-16 md:px-2': sidebarCollapsed,
        'md:w-64': ! sidebarCollapsed,
    }"
    aria-label="Sidebar dashboard"
>
    <div
        class="mb-6 flex items-center justify-between"
        x-bind:class="sidebarCollapsed && ! sidebarMobileOpen ? 'md:px-0' : 'px-2'"
    >
        <a
            href="{{ $routeUrl('public.home', '/') }}"
            class="flex min-w-0 items-center gap-2 rounded-field text-etc-on-surface transition hover:bg-etc-surface-container"
            x-bind:class="sidebarCollapsed && ! sidebarMobileOpen ? 'md:w-full md:justify-center md:p-0' : 'p-1 pr-2'"
            aria-label="ETC Padang"
        >
            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-selector bg-etc-magenta text-etc-surface shadow-soft">
                <span class="material-symbols-outlined text-xl" style="font-variation-settings: 'FILL' 1;">school</span>
            </span>
            <span
                x-show="! sidebarCollapsed || sidebarMobileOpen"
                x-transition.opacity
                class="truncate font-heading text-sm font-bold text-etc-on-surface"
            >
                ETC Padang
            </span>
        </a>

        <button
            type="button"
            class="flex h-8 w-8 items-center justify-center rounded-field text-etc-on-muted hover:bg-etc-surface-container hover:text-etc-magenta md:hidden"
            x-on:click="closeMobileSidebar()"
            aria-label="Tutup sidebar"
        >
            <span class="material-symbols-outlined">close</span>
        </button>
    </div>

    <nav aria-label="Navigasi dashboard" class="flex-1 space-y-1">
        @foreach ($items as $item)
            @php($isActive = $currentActive === $item['key'])
            <a
                href="{{ $item['url'] }}"
                x-on:click="closeMobileSidebar()"
                x-tooltip="{
                    content: sidebarCollapsed && ! sidebarMobileOpen ? @js($item['label']) : '',
                    theme: $store.theme,
                }"
                @class([
                    'flex min-h-8 items-center gap-3 rounded-field px-2.5 py-1.5 font-heading text-sm font-bold transition duration-200',
                    'bg-etc-surface-container text-etc-magenta shadow-soft ring-1 ring-etc-magenta/20' => $isActive,
                    'text-etc-on-muted hover:bg-etc-surface-container hover:text-etc-on-surface' => ! $isActive,
                ])
                x-bind:class="sidebarCollapsed && ! sidebarMobileOpen ? 'md:justify-center md:px-0' : ''"
                @if ($isActive) aria-current="page" @endif
                aria-label="{{ $item['label'] }}"
            >
                @if ($item['svg'] ?? null)
                    <x-ui.icon :name="$item['svg']" class="h-5 w-5 shrink-0" />
                @else
                    <span class="material-symbols-outlined shrink-0 text-[20px]" @if ($isActive) style="font-variation-settings: 'FILL' 1;" @endif>{{ $item['icon'] }}</span>
                @endif
                <span x-show="! sidebarCollapsed || sidebarMobileOpen" x-transition.opacity class="truncate">{{ $item['label'] }}</span>
            </a>
        @endforeach
    </nav>
</aside>
