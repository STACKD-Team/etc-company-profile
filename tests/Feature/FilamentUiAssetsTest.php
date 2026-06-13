<?php

use App\Models\User;
use App\Models\Program;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('loads the filament theme before app styles and includes core scripts on public pages', function () {
    $response = $this->get(route('auth.login'))->assertOk();
    $html = $response->getContent();
    $layout = file_get_contents(resource_path('views/components/layouts/public.blade.php'));
    $themePosition = strpos($layout, "FilamentAsset::getTheme('app')");
    $appAssetPosition = strpos($layout, '@vite');

    expect($html)->toContain('/css/filament/filament/app.css')
        ->and($html)->toContain('/js/filament/filament/app.js')
        ->and($html)->toContain('etc-filament-ui')
        ->and($themePosition)->not->toBeFalse()
        ->and($appAssetPosition)->not->toBeFalse()
        ->and($themePosition)->toBeLessThan($appAssetPosition);
});

it('loads filament theme components and core scripts on dashboard pages', function () {
    $instructor = User::factory()->create([
        'role' => 'instructor',
        'full_name' => 'Styled Instructor',
    ]);

    $this->actingAs($instructor)
        ->get(route('instructor.dashboard'))
        ->assertOk()
        ->assertSee('/css/filament/filament/app.css', false)
        ->assertSee('/js/filament/filament/app.js', false)
        ->assertSee('etc-filament-ui', false)
        ->assertSee('fi-section', false)
        ->assertSee('fi-empty-state', false)
        ->assertSee('fi-size-lg', false);
});

it('renders the shared dashboard shell with collapsible sidebar and profile logout menu', function () {
    $instructor = User::factory()->create([
        'role' => 'instructor',
        'full_name' => 'Header Profile Instructor',
    ]);

    $response = $this->actingAs($instructor)
        ->get(route('instructor.classes.index'))
        ->assertOk()
        ->assertSee('data-sidebar-toggle', false)
        ->assertSee('data-dashboard-profile-trigger', false)
        ->assertSee('etc-dashboard-sidebar-collapsed', false)
        ->assertSee('Header Profile Instructor')
        ->assertSee('ETC Padang')
        ->assertSee('aria-label="ETC Padang"', false)
        ->assertSee('action="'.route('auth.logout').'"', false)
        ->assertSee('id="dashboard-sidebar"', false)
        ->assertSee('md:w-16 md:px-2', false)
        ->assertSee('x-tooltip=', false)
        ->assertSee('x-data="{ profileMenuOpen: false }"', false)
        ->assertSee('x-show="profileMenuOpen"', false)
        ->assertSee('x-on:click.stop="profileMenuOpen = ! profileMenuOpen"', false)
        ->assertSee('style="display: none;"', false)
        ->assertSee('data-dashboard-profile-menu', false)
        ->assertSee('x-cloak', false)
        ->assertDontSee('etc-icon-button-outlined', false)
        ->assertDontSee('Navigasi dashboard mobile', false)
        ->assertDontSee('Bantuan')
        ->assertDontSee('ETC Planet</a>', false);

    $html = $response->getContent();
    $headerStart = strpos($html, '<header');
    $headerEnd = strpos($html, '</header>', $headerStart);
    $header = substr($html, $headerStart, $headerEnd - $headerStart);
    $mainStart = strpos($html, '<main');

    expect($header)
        ->not->toContain('Kelas Mengajar')
        ->and(strpos($html, 'Kelas Mengajar', $mainStart))
        ->not->toBeFalse()
        ->and(strpos($html, 'data-sidebar-toggle'))
        ->toBeLessThan(strpos($html, 'data-dashboard-profile-trigger'));
});

it('registers the ETC magenta palette as the global filament primary color', function () {
    $primary = FilamentColor::getColor('primary');

    expect($primary)->not->toBeNull()
        ->and($primary[500])->toBe(Color::convertToOklch('230, 0, 127'))
        ->and($primary[600])->toBe(Color::convertToOklch('185, 0, 101'));
});

it('uses the shared three-color component tokens and automatic datatable controls', function () {
    $css = file_get_contents(resource_path('css/app.css'));
    $design = file_get_contents(base_path('context/stitch_etc_planet_digital_hub/playful_professional_identity/DESIGN.md'));
    $table = file_get_contents(resource_path('views/components/ui/data-table.blade.php'));
    $pagination = file_get_contents(resource_path('views/components/ui/pagination.blade.php'));
    $javascript = file_get_contents(resource_path('js/app.js'));

    expect($css)
        ->toContain("vendor/filament/filament/resources/css/theme.css")
        ->toContain("@source '../../app/Filament/**/*.php'")
        ->toContain("@source '../../vendor/filament/**/*.blade.php'")
        ->toContain('--color-etc-magenta: #e6007f')
        ->toContain('--color-etc-charcoal: #27171c')
        ->toContain('--color-etc-surface: #f5f5f5')
        ->toContain('--radius-box: 1rem')
        ->toContain('--radius-field: 2rem')
        ->toContain('--radius-selector: 1rem')
        ->toContain('--etc-size-xs: 16px')
        ->toContain('--etc-size-md: 24px')
        ->toContain('--etc-size-xl: 32px')
        ->toContain('--border-component: 2px')
        ->toContain('var(--shadow-soft)')
        ->toContain('.etc-data-table table > thead > tr > th')
        ->toContain('.etc-data-table-scroll::-webkit-scrollbar-thumb')
        ->toContain('scrollbar-color: rgb(39 23 28 / 28%) transparent')
        ->not->toContain('.etc-data-table thead tr,')
        ->and($design)
        ->toContain("base: '#F5F5F5'")
        ->toContain("neutral: '#27171C'")
        ->toContain("accent: '#E6007F'")
        ->toContain('box: 1rem')
        ->toContain('field: 2rem')
        ->toContain('selector: 1rem')
        ->toContain('md: 24px')
        ->toContain('400ms')
        ->and($table)
        ->toContain('data-data-table-toolbar')
        ->toContain('etc-data-table-scroll')
        ->toContain('data-table-column-filter')
        ->toContain('data-table-filter-debounce')
        ->toContain('data-table-filter-immediate')
        ->toContain('x-ui.pagination')
        ->not->toContain('filter_drawer')
        ->not->toContain('slide-over')
        ->and($pagination)
        ->toContain('data-pagination')
        ->toContain('data-pagination-summary')
        ->toContain('withQueryString()->links()')
        ->and($javascript)
        ->toContain('setTimeout(submit, 400)')
        ->not->toContain('data-open-filter-drawer')
        ->not->toContain("new CustomEvent('open-modal'");
});

it('renders the datatable toolbar outside its panel and filters below column headings', function () {
    $instructor = User::factory()->create([
        'role' => 'instructor',
        'full_name' => 'Table Layout Instructor',
    ]);

    $response = $this->actingAs($instructor)
        ->get(route('instructor.classes.index'))
        ->assertOk()
        ->assertSee('data-data-table-toolbar', false)
        ->assertSee('data-pagination', false)
        ->assertSee('data-pagination-summary', false)
        ->assertSee('data-table-column-filter="name"', false)
        ->assertSee('data-table-column-filter="program"', false)
        ->assertSee('data-table-column-filter="schedule"', false)
        ->assertSee('data-table-column-filter="students"', false)
        ->assertSee('data-table-column-filter="status"', false)
        ->assertDontSee('data-open-filter-drawer', false);

    $html = $response->getContent();

    expect(strpos($html, 'data-data-table-toolbar'))
        ->toBeLessThan(strpos($html, 'etc-data-table'))
        ->and(substr_count($html, 'data-table-column-filter='))
        ->toBe(5)
        ->and($html)
        ->not->toContain('filter_drawer');
});

it('renders admin datatable column filters and applies safe filter sort query params', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    Program::query()->create([
        'name' => 'English Visible Program',
        'slug' => 'english-visible-program',
        'category' => 'english',
        'type' => 'regular',
        'target_age' => 'teen',
        'price' => 1200000,
        'registration_fee' => 200000,
        'is_active' => true,
    ]);

    Program::query()->create([
        'name' => 'Mandarin Hidden Program',
        'slug' => 'mandarin-hidden-program',
        'category' => 'mandarin',
        'type' => 'private',
        'target_age' => 'adult',
        'price' => 1400000,
        'registration_fee' => 200000,
        'is_active' => false,
    ]);

    $this->actingAs($admin)
        ->get(route('admin.program.index', [
            'search' => 'Visible',
            'category' => 'english',
            'is_active' => '1',
            'sort' => 'name',
            'direction' => 'desc',
        ]))
        ->assertOk()
        ->assertSee('data-table-column-filter="category"', false)
        ->assertSee('data-table-column-filter="type"', false)
        ->assertSee('data-table-column-filter="target_age"', false)
        ->assertSee('data-table-column-filter="is_active"', false)
        ->assertSee('English Visible Program')
        ->assertDontSee('Mandarin Hidden Program');
});
