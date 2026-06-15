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
            ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'url' => '/admin/dashboard', 'key' => 'dashboard', 'icon' => 'dashboard'],
            [
                'label' => 'Penerimaan',
                'key' => 'admissions',
                'icon' => 'assignment_ind',
                'children' => [
                    ['label' => 'Pendaftaran', 'route' => 'admin.registration.index', 'url' => '#', 'key' => 'registrations', 'icon' => 'assignment'],
                    ['label' => 'Pembayaran', 'route' => 'admin.payment.index', 'url' => '#', 'key' => 'payments', 'icon' => 'payments'],
                    ['label' => 'Placement', 'route' => 'admin.placement-test.index', 'url' => '#', 'key' => 'placement-test', 'icon' => 'event_available'],
                ],
            ],
            [
                'label' => 'Akademik',
                'key' => 'academic',
                'icon' => 'school',
                'children' => [
                    ['label' => 'Program', 'route' => 'admin.program.index', 'url' => '#', 'key' => 'programs', 'icon' => 'school'],
                    ['label' => 'Kelas', 'route' => 'admin.class.index', 'url' => '#', 'key' => 'classes', 'icon' => 'meeting_room'],
                    ['label' => 'Room', 'route' => 'admin.room.index', 'url' => '#', 'key' => 'rooms', 'icon' => 'door_open'],
                    ['label' => 'Enrollment', 'route' => 'admin.enrollment.index', 'url' => '#', 'key' => 'enrollments', 'icon' => 'how_to_reg'],
                    ['label' => 'Rapor', 'route' => 'admin.report-card.index', 'url' => '#', 'key' => 'reports', 'icon' => 'description'],
                ],
            ],
            [
                'label' => 'Pengguna',
                'key' => 'users',
                'icon' => 'group',
                'children' => [
                    ['label' => 'Siswa', 'route' => 'admin.student.index', 'url' => '#', 'key' => 'students', 'icon' => 'groups'],
                    ['label' => 'Instructor', 'route' => 'admin.instructor.index', 'url' => '#', 'key' => 'instructors', 'icon' => 'co_present'],
                ],
            ],
            [
                'label' => 'CMS',
                'key' => 'cms',
                'icon' => 'widgets',
                'children' => [
                    ['label' => 'Reels', 'route' => 'admin.reel.index', 'url' => '#', 'key' => 'reels', 'icon' => 'smart_display'],
                    ['label' => 'Gallery', 'route' => 'admin.gallery.index', 'url' => '#', 'key' => 'gallery', 'icon' => 'photo_library'],
                    ['label' => 'Partner', 'route' => 'admin.partner.index', 'url' => '#', 'key' => 'partner', 'icon' => 'handshake'],
                    ['label' => 'Testimonial', 'route' => 'admin.testimonial.index', 'url' => '#', 'key' => 'testimonial', 'icon' => 'reviews'],
                    ['label' => 'FAQ', 'route' => 'admin.faq.index', 'url' => '#', 'key' => 'faq', 'icon' => 'quiz'],
                    ['label' => 'Profile', 'route' => 'admin.profile.index', 'url' => '#', 'key' => 'profile', 'icon' => 'settings'],
                ],
            ],
            [
                'label' => 'Komunikasi',
                'key' => 'communication',
                'icon' => 'forum',
                'children' => [
                    ['label' => 'Pesan Kontak', 'route' => 'admin.contact-message.index', 'url' => '#', 'key' => 'contact_messages', 'icon' => 'inbox'],
                    ['label' => 'Chatbot Logs', 'route' => 'admin.chatbot-log.index', 'url' => '#', 'key' => 'chatbot_logs', 'icon' => 'smart_toy'],
                ],
            ],
        ],
        'student' => [
            ['label' => 'Dashboard', 'route' => 'student.dashboard', 'url' => '#', 'key' => 'dashboard', 'svg' => 'nav-dashboard'],
            ['label' => 'Profil Saya', 'route' => 'student.profile.show', 'url' => '#', 'key' => 'profile', 'svg' => 'nav-profile'],
            ['label' => 'Kelas Saya', 'route' => 'student.classes.index', 'url' => '#', 'key' => 'classes', 'svg' => 'nav-class'],
            ['label' => 'Rapor', 'route' => 'student.report-cards.index', 'url' => '#', 'key' => 'reports', 'svg' => 'nav-report'],
            ['label' => 'Riwayat Pembayaran', 'route' => 'student.payments.index', 'url' => '#', 'key' => 'payments', 'svg' => 'nav-payment'],
            ['label' => 'Bantuan', 'route' => 'student.help.index', 'url' => '#', 'key' => 'help', 'icon' => 'help'],
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
        $item['children'] = collect($item['children'] ?? [])->map(function (array $child) use ($routeUrl) {
            $child['key'] ??= str($child['label'] ?? 'item')->slug()->toString();
            $child['url'] = $routeUrl($child['route'] ?? null, $child['url'] ?? '#');
            $child['icon'] ??= 'circle';

            return $child;
        })->all();

        return $item;
    });

    $routeActive = $items
        ->flatMap(fn (array $item) => $item['children'] !== [] ? $item['children'] : [$item])
        ->first(function (array $item): bool {
            $routeName = $item['route'] ?? null;

            return $routeName
                && \Illuminate\Support\Facades\Route::has($routeName)
                && request()->routeIs($routeName);
        });
    $currentActive = $active ?: ($routeActive['key'] ?? 'dashboard');
@endphp

<aside
    id="dashboard-sidebar"
    {{ $attributes->class('fixed inset-y-0 left-0 z-50 flex w-72 flex-shrink-0 -translate-x-full flex-col overflow-y-auto border-r-2 border-etc-outline-variant bg-etc-surface px-3 py-4 text-etc-on-surface shadow-soft md:relative md:z-auto md:w-64 md:translate-x-0') }}
    x-bind:class="sidebarCollapsed && ! sidebarMobileOpen ? 'md:w-16 md:px-2' : 'md:w-64'"
    aria-label="Sidebar dashboard"
    data-dashboard-sidebar
>
    <div class="mb-6 flex items-center gap-2 px-2" data-sidebar-brand-row>
        <a
            href="{{ $routeUrl('public.home', '/') }}"
            class="flex min-w-0 flex-1 items-center gap-2 rounded-field p-1 pr-2 text-etc-on-surface transition hover:bg-etc-surface-container"
            aria-label="ETC Padang"
            data-sidebar-brand-link
        >
            <span class="flex h-[var(--etc-selector-size-xl)] w-[var(--etc-selector-size-xl)] shrink-0 items-center justify-center rounded-selector bg-etc-magenta text-etc-surface shadow-soft">
                <span class="material-symbols-outlined text-xl" style="font-variation-settings: 'FILL' 1;">school</span>
            </span>
            <span
                class="truncate font-heading text-sm font-bold text-etc-on-surface"
                data-sidebar-label
                data-sidebar-brand-label
            >
                ETC Padang
            </span>
        </a>

        <button
            type="button"
            class="flex h-[var(--etc-field-size-sm)] w-[var(--etc-field-size-sm)] shrink-0 items-center justify-center rounded-field text-etc-on-muted hover:bg-etc-surface-container hover:text-etc-magenta"
            aria-controls="dashboard-sidebar"
            data-sidebar-toggle
            data-sidebar-primary-toggle
        >
            <span
                class="material-symbols-outlined"
                data-sidebar-toggle-icon
            >menu</span>
        </button>
    </div>

    <nav aria-label="Navigasi dashboard" class="flex-1 space-y-1">
        @foreach ($items as $item)
            @php
                $children = collect($item['children'] ?? []);
                $isActive = $currentActive === $item['key'] || $children->contains(fn (array $child) => $currentActive === $child['key']);
            @endphp

            @if ($children->isNotEmpty())
                <details
                    class="group"
                    @if ($isActive) open @endif
                    data-sidebar-nav-group
                    data-sidebar-group="{{ $item['key'] }}"
                >
                    <summary
                        x-tooltip="{
                            content: sidebarCollapsed && ! sidebarMobileOpen ? @js($item['label']) : '',
                            theme: $store.theme,
                        }"
                        @class([
                            'flex min-h-[var(--etc-field-size-sm)] cursor-pointer list-none items-center gap-3 rounded-field px-4 py-1.5 font-heading text-sm font-bold transition duration-200 [&::-webkit-details-marker]:hidden',
                            'bg-etc-surface-container text-etc-magenta shadow-soft ring-1 ring-etc-magenta/20' => $isActive,
                            'text-etc-on-muted hover:bg-etc-surface-container hover:text-etc-on-surface' => ! $isActive,
                        ])
                        aria-label="{{ $item['label'] }}"
                    >
                        <span class="material-symbols-outlined shrink-0 text-[20px]" @if ($isActive) style="font-variation-settings: 'FILL' 1;" @endif>{{ $item['icon'] }}</span>
                        <span class="truncate" data-sidebar-label>{{ $item['label'] }}</span>
                        <span class="material-symbols-outlined ml-auto text-[18px]" data-sidebar-label>expand_more</span>
                    </summary>
                    <div class="mt-1 space-y-1 pl-7" data-sidebar-label>
                        @foreach ($children as $child)
                            @php($childActive = $currentActive === $child['key'])
                            <a
                                href="{{ $child['url'] }}"
                                @class([
                                    'flex min-h-9 items-center gap-2 rounded-field px-3 py-1.5 font-heading text-xs font-bold transition',
                                    'bg-etc-surface-container text-etc-magenta' => $childActive,
                                    'text-etc-on-muted hover:bg-etc-surface-container hover:text-etc-on-surface' => ! $childActive,
                                ])
                                @if ($childActive) aria-current="page" @endif
                                aria-label="{{ $child['label'] }}"
                                data-sidebar-nav-link
                            >
                                <span class="material-symbols-outlined shrink-0 text-[18px]">{{ $child['icon'] }}</span>
                                <span class="truncate">{{ $child['label'] }}</span>
                            </a>
                        @endforeach
                    </div>
                </details>
            @else
                <a
                    href="{{ $item['url'] }}"
                    x-tooltip="{
                        content: sidebarCollapsed && ! sidebarMobileOpen ? @js($item['label']) : '',
                        theme: $store.theme,
                    }"
                    @class([
                        'flex min-h-[var(--etc-field-size-sm)] items-center gap-3 rounded-field px-4 py-1.5 font-heading text-sm font-bold transition duration-200',
                        'bg-etc-surface-container text-etc-magenta shadow-soft ring-1 ring-etc-magenta/20' => $isActive,
                        'text-etc-on-muted hover:bg-etc-surface-container hover:text-etc-on-surface' => ! $isActive,
                    ])
                    @if ($isActive) aria-current="page" @endif
                    aria-label="{{ $item['label'] }}"
                    data-sidebar-nav-link
                >
                    @if ($item['svg'] ?? null)
                        <x-ui.icon :name="$item['svg']" class="h-5 w-5 shrink-0" />
                    @else
                        <span class="material-symbols-outlined shrink-0 text-[20px]" @if ($isActive) style="font-variation-settings: 'FILL' 1;" @endif>{{ $item['icon'] }}</span>
                    @endif
                    <span class="truncate" data-sidebar-label>{{ $item['label'] }}</span>
                </a>
            @endif
        @endforeach
    </nav>
</aside>
