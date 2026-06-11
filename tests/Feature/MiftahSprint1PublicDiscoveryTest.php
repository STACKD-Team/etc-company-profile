<?php

use App\Models\Content;
use App\Models\Program;
use App\Models\ProgramPromotion;
use App\Models\Registration;
use App\Models\Reel;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function miftahSprint1Program(array $overrides = []): Program
{
    return Program::query()->create(array_merge([
        'name' => 'Sprint 1 Intensive English',
        'slug' => 'sprint-1-intensive-english',
        'category' => 'english',
        'type' => 'regular',
        'target_age' => 'teen',
        'description' => 'Kelas intensif untuk speaking, vocabulary, dan confidence.',
        'duration_meetings' => 24,
        'max_students' => 10,
        'price' => 2000000,
        'registration_fee' => 200000,
        'thumbnail' => 'programs/thumbnails/intensive.jpg',
        'is_active' => true,
    ], $overrides));
}

function miftahSprint1Promotion(Program $program, array $overrides = []): ProgramPromotion
{
    return ProgramPromotion::query()->create(array_merge([
        'program_id' => $program->id,
        'title' => 'Sprint Deal',
        'description' => 'Potongan untuk pendaftaran awal.',
        'discount_type' => 'fixed',
        'discount_value' => 300000,
        'starts_at' => now()->subDay(),
        'ends_at' => now()->addWeek(),
        'is_active' => true,
        'badge_label' => 'Hemat 300K',
        'terms' => 'Berlaku untuk kuota awal dan tidak dapat digabung dengan promo lain.',
    ], $overrides));
}

function miftahSprint1Reel(array $overrides = []): Reel
{
    return Reel::query()->create(array_merge([
        'title' => 'Vertical Sprint 1 Reel',
        'description' => 'Suasana kelas speaking ETC Planet.',
        'video_path' => 'videos/video1.mp4',
        'thumbnail_path' => 'images/pu1-img.jpg',
        'category' => 'edukasi',
        'views_count' => 11,
        'likes_count' => 3,
        'is_published' => true,
        'published_at' => now(),
    ], $overrides));
}

function miftahSprint1RegistrationPayload(Program $program, array $overrides = []): array
{
    return array_merge([
        'program_id' => $program->id,
        'applying_for' => 'smp_teen',
        'full_name' => 'Miftah Sprint One',
        'email' => 'miftah.sprint1@example.test',
        'mobile_phone' => '081234567890',
        'place_of_birth' => 'Padang',
        'date_of_birth' => '2005-01-01',
        'sex' => 'F',
        'religion' => 'Islam',
        'nationality' => 'Indonesia',
        'occupation_school' => 'SMP Padang',
        'nisn' => '1234567890',
        'nik' => '1371010101010001',
        'kps_receiver' => '0',
        'no_kps' => null,
        'worthy_of_pip' => '0',
        'pip_reason' => null,
        'no_kip' => null,
        'address' => 'Jl. Sprint 1 No. 1',
        'rt_rw' => '001/002',
        'postal_code' => '25111',
        'village' => 'Ulak Karang',
        'sub_district' => 'Padang Utara',
        'district' => 'Padang',
        'province' => 'Sumatera Barat',
        'living_with' => 'Orang Tua',
        'transportation' => 'Kendaraan Pribadi',
        'mother_name' => 'Ibu Sprint',
        'father_name' => 'Ayah Sprint',
        'preferred_days' => 'mon_wed',
        'preferred_time' => '09.00-10.30',
    ], $overrides);
}

it('shows active program promo and cover image on listing and detail only', function () {
    $program = miftahSprint1Program();
    miftahSprint1Promotion($program);
    miftahSprint1Promotion($program, [
        'title' => 'Inactive Promo',
        'badge_label' => 'Hidden Inactive',
        'is_active' => false,
    ]);
    miftahSprint1Promotion($program, [
        'title' => 'Future Promo',
        'badge_label' => 'Hidden Future',
        'starts_at' => now()->addDay(),
        'ends_at' => now()->addWeek(),
    ]);
    miftahSprint1Promotion($program, [
        'title' => 'Expired Promo',
        'badge_label' => 'Hidden Expired',
        'starts_at' => now()->subMonth(),
        'ends_at' => now()->subDay(),
    ]);

    $this->get(route('public.programs.index'))
        ->assertOk()
        ->assertSee('data-sprint1-program-card', false)
        ->assertSee('data-program-cover', false)
        ->assertSee('Hemat 300K')
        ->assertSee('Rp 2.000.000')
        ->assertSee('Rp 1.700.000')
        ->assertSee('Rp 300.000')
        ->assertSee('programs/thumbnails/intensive.jpg')
        ->assertDontSee('Hidden Inactive')
        ->assertDontSee('Hidden Future')
        ->assertDontSee('Hidden Expired');

    $this->get(route('public.programs.show', $program))
        ->assertOk()
        ->assertSee('data-program-cover', false)
        ->assertSee('data-sprint1-pricing-panel', false)
        ->assertSee('Sprint Deal')
        ->assertSee('Hemat 300K')
        ->assertSee('Rp 1.700.000')
        ->assertSee('Potongan Rp 300.000')
        ->assertSee('data-promo-terms', false)
        ->assertSee('programs/thumbnails/intensive.jpg')
        ->assertDontSee('Hidden Inactive')
        ->assertDontSee('Hidden Future')
        ->assertDontSee('Hidden Expired');
});

it('calculates percentage promo for display without changing registration payment amount', function () {
    $program = miftahSprint1Program([
        'slug' => 'sprint-1-percentage-promo',
        'price' => 2000000,
        'registration_fee' => 200000,
    ]);
    miftahSprint1Promotion($program, [
        'title' => 'Quarter Discount',
        'discount_type' => 'percentage',
        'discount_value' => 25,
        'badge_label' => 'Diskon 25%',
    ]);

    $this->get(route('public.programs.show', $program))
        ->assertOk()
        ->assertSee('Diskon 25%')
        ->assertSee('Rp 1.500.000')
        ->assertSee('Potongan Rp 500.000')
        ->assertSee(route('registrations.programs.index', ['program' => $program->id]), false);

    $this->post(route('registrations.store'), miftahSprint1RegistrationPayload($program))
        ->assertRedirect();

    $registration = Registration::query()->firstOrFail();

    expect((float) $registration->payment_amount)->toBe(2200000.0);
});

it('shows only published partners on the public home page', function () {
    Content::query()->create([
        'type' => 'partner',
        'title' => 'Published School Partner',
        'slug' => 'published-school-partner',
        'body' => 'Kolaborasi kelas speaking untuk siswa sekolah.',
        'image' => 'images/partner-school.jpg',
        'meta' => ['category' => 'Sekolah', 'since' => '2025', 'website' => 'https://partner.example.test'],
        'is_published' => true,
    ]);

    Content::query()->create([
        'type' => 'partner',
        'title' => 'Hidden Partner',
        'slug' => 'hidden-partner',
        'body' => 'Tidak tampil.',
        'is_published' => false,
    ]);

    $this->get(route('public.home'))
        ->assertOk()
        ->assertSee('data-partner-section', false)
        ->assertSee('Kerja Sama ETC')
        ->assertSee('Published School Partner')
        ->assertSee('Sekolah')
        ->assertSee('Sejak 2025')
        ->assertSee('partner-school.jpg')
        ->assertDontSee('Hidden Partner');
});

it('renders gallery from published CMS gallery with multiple images and empty state', function () {
    Content::query()->create([
        'type' => 'gallery',
        'title' => 'Published Gallery Activity',
        'slug' => 'published-gallery-activity',
        'body' => 'Dokumentasi kelas conversation.',
        'image' => 'images/gallery-main.jpg',
        'images' => ['images/gallery-extra-1.jpg', 'images/gallery-extra-2.jpg'],
        'meta' => ['event_date' => '2026-06-10', 'location' => 'ETC Planet Padang', 'category' => 'Speaking'],
        'is_published' => true,
    ]);

    Content::query()->create([
        'type' => 'gallery',
        'title' => 'Hidden Gallery Activity',
        'slug' => 'hidden-gallery-activity',
        'image' => 'images/hidden-gallery.jpg',
        'is_published' => false,
    ]);

    $this->get(route('public.gallery.index'))
        ->assertOk()
        ->assertSee('data-gallery-card', false)
        ->assertSee('Published Gallery Activity')
        ->assertSee('images/gallery-main.jpg')
        ->assertSee('images/gallery-extra-1.jpg')
        ->assertSee('images/gallery-extra-2.jpg')
        ->assertSee('Speaking')
        ->assertDontSee('Hidden Gallery Activity');

    Content::query()->delete();

    $this->get(route('public.gallery.index'))
        ->assertOk()
        ->assertSee('Galeri kegiatan belum tersedia');
});

it('uses vertical reels experience while keeping published only access', function () {
    $published = miftahSprint1Reel(['title' => 'Published Vertical Reel']);
    $draft = miftahSprint1Reel([
        'title' => 'Draft Vertical Reel',
        'is_published' => false,
        'published_at' => null,
    ]);

    $this->get(route('public.reels.index'))
        ->assertOk()
        ->assertSee('data-vertical-reels-feed', false)
        ->assertSee('data-reel-slide', false)
        ->assertSee('data-view-endpoint', false)
        ->assertSee('data-like-endpoint', false)
        ->assertSee('Published Vertical Reel')
        ->assertDontSee('Draft Vertical Reel');

    $this->get(route('public.reels.show', $published))
        ->assertOk()
        ->assertSee('data-vertical-reel-detail', false)
        ->assertSee('aspect-[9/16]', false)
        ->assertSee('data-view-endpoint', false)
        ->assertSee('data-like-endpoint', false);

    $this->get(route('public.reels.show', $draft))->assertNotFound();
    $this->postJson(route('public.reels.views.store', $draft))->assertNotFound();
    $this->postJson(route('public.reels.likes.store', $draft))->assertNotFound();
});

it('keeps public discovery copy free from implementation notes', function () {
    miftahSprint1Program();
    miftahSprint1Reel();

    foreach ([
        route('public.home'),
        route('public.about'),
        route('public.team.index'),
        route('public.facilities.index'),
        route('public.gallery.index'),
        route('public.faq.index'),
        route('public.programs.index'),
        route('public.reels.index'),
    ] as $url) {
        $this->get($url)
            ->assertOk()
            ->assertDontSee('contents type')
            ->assertDontSee('content type')
            ->assertDontSee('show_on_team_page')
            ->assertDontSee('sprint berikutnya')
            ->assertDontSee('Untuk Sprint 1');
    }
});

it('keeps public discovery controls on shared blade components', function () {
    $controlFiles = [
        'views/components/site/navbar.blade.php',
        'views/components/site/chatbot.blade.php',
        'views/components/site/footer.blade.php',
        'views/public/faq/index.blade.php',
        'views/public/programs/index.blade.php',
        'views/public/programs/show.blade.php',
        'views/public/reels/index.blade.php',
        'views/public/reels/show.blade.php',
        'views/public/registration/create.blade.php',
        'views/registration/programs/index.blade.php',
    ];

    foreach ($controlFiles as $file) {
        $source = file_get_contents(resource_path($file));

        expect($source)
            ->not->toMatch('/<(button|input|select|textarea|summary|details)\b/i')
            ->and($source)->toContain('<x-ui.');
    }

    foreach ([
        'views/components/site/navbar.blade.php',
        'views/components/site/footer.blade.php',
        'views/public/registration/create.blade.php',
        'views/registration/programs/index.blade.php',
    ] as $file) {
        expect(file_get_contents(resource_path($file)))->not->toMatch('/<a\b/i');
    }

    foreach ([
        'views/public/about.blade.php',
        'views/public/facilities/index.blade.php',
        'views/public/gallery/index.blade.php',
        'views/public/home.blade.php',
        'views/public/programs/index.blade.php',
        'views/public/programs/show.blade.php',
    ] as $file) {
        expect(file_get_contents(resource_path($file)))
            ->not->toContain('public-chip')
            ->not->toContain('public-link');
    }
});
