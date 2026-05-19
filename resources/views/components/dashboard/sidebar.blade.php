@props([
    'area' => 'student',
    'items' => null,
    'user' => null,
    'active' => null,
    'helpUrl' => null,
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
            ['label' => 'Pendaftaran', 'route' => 'admin.registrations.index', 'url' => '#', 'key' => 'registrations', 'icon' => 'how_to_reg'],
            ['label' => 'Pembayaran', 'route' => 'admin.payments.index', 'url' => '#', 'key' => 'payments', 'icon' => 'payments'],
            ['label' => 'Placement Test', 'route' => 'admin.placement-tests.index', 'url' => '#', 'key' => 'placement', 'icon' => 'assignment'],
            ['label' => 'Data Siswa', 'route' => 'admin.students.index', 'url' => '#', 'key' => 'students', 'icon' => 'groups'],
            ['label' => 'Program', 'route' => 'admin.programs.index', 'url' => '#', 'key' => 'programs', 'icon' => 'school'],
            ['label' => 'Kelas', 'route' => 'admin.classes.index', 'url' => '#', 'key' => 'classes', 'icon' => 'meeting_room'],
            ['label' => 'Rapor', 'route' => 'admin.report-cards.index', 'url' => '#', 'key' => 'reports', 'icon' => 'description'],
            ['label' => 'Reels', 'route' => 'admin.reels.index', 'url' => '#', 'key' => 'reels', 'icon' => 'smart_display'],
            ['label' => 'CMS Konten', 'route' => 'admin.contents.index', 'url' => '#', 'key' => 'contents', 'icon' => 'dashboard_customize'],
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

    $items = collect($items ?? $defaultItems[$area] ?? $defaultItems['student'])->map(function (array $item) use ($routeUrl) {
        $item['key'] ??= str($item['label'] ?? 'item')->slug()->toString();
        $item['url'] = $routeUrl($item['route'] ?? null, $item['url'] ?? '#');
        $item['icon'] ??= 'circle';

        return $item;
    });

    $currentActive = $active ?: $items->first(function (array $item) {
        $routeName = $item['route'] ?? null;

        if ($routeName && \Illuminate\Support\Facades\Route::has($routeName) && request()->routeIs($routeName)) {
            return true;
        }

        return false;
    })['key'] ?? 'dashboard';

    $user ??= auth()->user();
    $displayName = $user?->full_name ?? $user?->name ?? str($area)->headline()->toString();
    $displayRole = str($area)->headline()->toString();
    $avatar = $user?->avatar ?? null;
    $initial = str($displayName)->trim()->substr(0, 1)->upper()->toString();
    $helpUrl ??= $routeUrl($area === 'student' ? 'student.help.index' : null, '#');
@endphp

<aside {{ $attributes->class('hidden h-screen w-64 flex-shrink-0 flex-col overflow-y-auto border-r border-white/5 bg-etc-charcoal px-4 py-8 text-white shadow-xl md:flex') }}>
    <div class="mb-10 px-4">
        <a href="{{ $routeUrl('home', '/') }}" class="font-heading text-xl font-black tracking-normal text-white">
            ETC Planet
        </a>

        <div class="mt-6 flex items-center gap-3">
            <div class="flex h-11 w-11 items-center justify-center overflow-hidden rounded-full bg-etc-magenta font-heading text-sm font-bold text-white">
                @if ($avatar)
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($avatar) }}" alt="{{ $displayName }}" class="h-full w-full object-cover">
                @else
                    {{ $initial }}
                @endif
            </div>
            <div class="min-w-0">
                <p class="truncate font-heading text-sm font-bold text-white">{{ $displayName }}</p>
                <p class="text-xs text-zinc-400">{{ $displayRole }}</p>
            </div>
        </div>
    </div>

    <nav aria-label="Navigasi dashboard" class="flex-1 space-y-2">
        @foreach ($items as $item)
            @php($isActive = $currentActive === $item['key'])
            <a
                href="{{ $item['url'] }}"
                @class([
                    'flex items-center gap-3 rounded-lg px-4 py-3 font-heading text-sm font-bold transition duration-200',
                    'border-r-4 border-etc-magenta bg-white/10 text-etc-magenta' => $isActive,
                    'text-zinc-400 hover:translate-x-1 hover:bg-white/5 hover:text-white' => ! $isActive,
                ])
                @if ($isActive) aria-current="page" @endif
            >
                @if ($item['svg'] ?? null)
                    <x-ui.icon :name="$item['svg']" class="h-5 w-5 shrink-0" />
                @else
                    <span class="material-symbols-outlined text-xl" @if ($isActive) style="font-variation-settings: 'FILL' 1;" @endif>{{ $item['icon'] }}</span>
                @endif
                <span>{{ $item['label'] }}</span>
            </a>
        @endforeach
    </nav>

    <div class="mt-8 space-y-3 px-4">
        @isset($actions)
            {{ $actions }}
        @else
            <a href="{{ $helpUrl }}" class="flex min-h-12 items-center justify-center gap-2 rounded-full border border-zinc-600 px-4 py-3 font-heading text-sm font-bold text-zinc-300 transition hover:border-white hover:text-white">
                <x-ui.icon name="help" class="h-4 w-4" />
                Bantuan
            </a>

            @if (\Illuminate\Support\Facades\Route::has('logout'))
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex min-h-12 w-full items-center justify-center gap-2 rounded-full bg-white/5 px-4 py-3 font-heading text-sm font-bold text-zinc-300 transition hover:bg-white/10 hover:text-white">
                        <span class="material-symbols-outlined text-lg">logout</span>
                        Keluar
                    </button>
                </form>
            @endif
        @endisset
    </div>
</aside>

<nav aria-label="Navigasi dashboard mobile" class="fixed inset-x-0 bottom-0 z-50 flex items-center justify-around rounded-t-3xl border-t border-white/10 bg-etc-charcoal px-4 py-3 shadow-[0_-12px_30px_rgba(0,0,0,0.28)] md:hidden">
    @foreach ($items->take(4) as $item)
        @php($isActive = $currentActive === $item['key'])
        <a
            href="{{ $item['url'] }}"
            @class([
                'flex w-16 flex-col items-center justify-center gap-1 rounded-2xl p-2 font-heading text-[10px] font-bold uppercase transition',
                'scale-105 bg-white/5 text-etc-magenta' => $isActive,
                'text-zinc-400 active:bg-white/10' => ! $isActive,
            ])
            @if ($isActive) aria-current="page" @endif
        >
            @if ($item['svg'] ?? null)
                <x-ui.icon :name="$item['svg']" class="h-5 w-5 shrink-0" />
            @else
                <span class="material-symbols-outlined text-xl" @if ($isActive) style="font-variation-settings: 'FILL' 1;" @endif>{{ $item['icon'] }}</span>
            @endif
            <span>{{ str($item['label'])->words(1, '')->toString() }}</span>
        </a>
    @endforeach
</nav>
