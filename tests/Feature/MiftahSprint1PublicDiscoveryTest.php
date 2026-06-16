<?php

use App\Models\Content;
use App\Models\Program;
use App\Models\ProgramPromotion;
use App\Models\Reel;
use App\Models\Registration;
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
        ->assertSee('public-program-card__description', false)
        ->assertSee('public-program-card__pricing', false)
        ->assertSee('public-program-card__registration-fee', false)
        ->assertSee('public-program-card__actions', false)
        ->assertSee(route('registrations.create', ['program' => $program->id]), false)
        ->assertDontSee(route('registrations.programs.index', ['program' => $program->id]), false)
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
        ->assertSee(route('registrations.create', ['program' => $program->id]), false)
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
        ->assertSee(route('registrations.create', ['program' => $program->id]), false);

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
    $selected = miftahSprint1Reel([
        'title' => 'Selected Vertical Reel',
        'published_at' => now()->subDay(),
    ]);
    $draft = miftahSprint1Reel([
        'title' => 'Draft Vertical Reel',
        'is_published' => false,
        'published_at' => null,
    ]);

    $this->get(route('public.reels.index'))
        ->assertOk()
        ->assertSee('data-vertical-reels-feed', false)
        ->assertSee('data-reel-slide', false)
        ->assertSee('data-autoplay-reel="true"', false)
        ->assertSee('data-view-endpoint', false)
        ->assertSee('data-reel-sound-control', false)
        ->assertSee('data-reel-sound-toggle', false)
        ->assertSee('data-reel-view-count', false)
        ->assertSee('data-like-endpoint', false)
        ->assertSee('data-reel-like', false)
        ->assertSee('public-reel-actions', false)
        ->assertDontSee('data-reel-volume-down', false)
        ->assertDontSee('data-reel-volume-up', false)
        ->assertDontSee('muted', false)
        ->assertSee('Published Vertical Reel')
        ->assertSee('Selected Vertical Reel')
        ->assertDontSee('Draft Vertical Reel');

    $this->get(route('public.reels.index', ['reel' => $selected->getKey()]))
        ->assertOk()
        ->assertSeeInOrder([
            'data-reel-id="'.$selected->getKey().'"',
            'Selected Vertical Reel',
            'data-reel-id="'.$published->getKey().'"',
            'Published Vertical Reel',
        ], false);

    $this->get(route('public.reels.show', $published))
        ->assertRedirect(route('public.reels.index', ['reel' => $published->getKey()]));

    $this->get(route('public.reels.show', $draft))->assertNotFound();
    $this->postJson(route('public.reels.views.store', $draft))->assertNotFound();
    $this->postJson(route('public.reels.likes.store', $draft))->assertNotFound();
});

it('shows Miftah discovery destinations in the public navbar', function () {
    $response = $this->get(route('public.home'))->assertOk();

    foreach ([
        'public.team.index',
        'public.facilities.index',
        'public.gallery.index',
        'public.faq.index',
    ] as $routeName) {
        $response->assertSee(route($routeName), false);
    }

    $response
        ->assertSee('Team')
        ->assertSee('Fasilitas')
        ->assertSee('Galeri')
        ->assertSee('FAQ');
});

it('renders a public discovery footer with real website destinations only', function () {
    Content::query()->create([
        'type' => 'profile',
        'title' => 'ETC Planet',
        'slug' => 'etc-profile',
        'body' => 'Profil resmi ETC Planet.',
        'meta' => [
            'address' => 'Jl. S. Parman No. 202B, Ulak Karang Selatan, Padang',
            'instagram' => 'https://www.instagram.com/etcplanet/',
        ],
        'is_published' => true,
    ]);

    $response = $this->get(route('public.home'))
        ->assertOk()
        ->assertSee('Jl. S. Parman No. 202B, Ulak Karang Selatan, Padang')
        ->assertSee('Education Tutorial Centre Padang')
        ->assertDontSee('English Training Center')
        ->assertSee('https://www.instagram.com/etcplanet/', false)
        ->assertSee('Instagram')
        ->assertDontSee('Kebijakan Privasi')
        ->assertDontSee('Syarat &amp; Ketentuan', false)
        ->assertDontSee('Karir')
        ->assertDontSee('hello@etcplanet.test')
        ->assertDontSee('+62 812-0000-0000');

    foreach ([
        'public.about',
        'public.team.index',
        'public.facilities.index',
        'public.gallery.index',
        'public.programs.index',
        'public.reels.index',
        'public.faq.index',
        'public.contact.index',
    ] as $routeName) {
        $response->assertSee(route($routeName), false);
    }

    $footerSource = file_get_contents(resource_path('views/components/public-discovery/footer.blade.php'));

    expect($footerSource)
        ->toContain('public-discovery-footer__instagram')
        ->not->toContain('!border');
});

it('uses one consistent footer across every non fullscreen Miftah discovery page', function () {
    $program = miftahSprint1Program();

    foreach ([
        route('public.home'),
        route('public.about'),
        route('public.team.index'),
        route('public.facilities.index'),
        route('public.gallery.index'),
        route('public.contact.index'),
        route('public.faq.index'),
        route('public.programs.index'),
        route('public.programs.show', $program),
    ] as $url) {
        $content = $this->get($url)
            ->assertOk()
            ->assertSee('data-public-discovery-footer', false)
            ->assertSee('data-public-discovery-page-end', false)
            ->assertSee('data-chatbot-widget', false)
            ->assertSee('Education Tutorial Centre Padang')
            ->getContent();

        expect(substr_count($content, 'data-public-discovery-footer'))->toBe(1);
        expect(substr_count($content, 'data-public-discovery-page-end'))->toBe(1);
        expect(substr_count($content, 'data-chatbot-widget'))->toBe(1);
    }
});

it('uses the same Miftah public chrome throughout the pre login registration flow', function () {
    foreach ([
        resource_path('views/public/registration/create.blade.php'),
        resource_path('views/public/registration/payment.blade.php'),
        resource_path('views/public/registration/confirmation.blade.php'),
        resource_path('views/public/placeholder.blade.php'),
    ] as $viewPath) {
        $source = file_get_contents($viewPath);

        expect($source)
            ->toContain(':show-navbar="false"')
            ->toContain(':show-footer="false"')
            ->toContain(':show-chatbot="false"')
            ->toContain('<x-public-discovery.navbar')
            ->toContain('<x-public-discovery.page-end />')
            ->not->toContain('<x-site.footer')
            ->not->toContain('<x-site.chatbot');
    }

    $pageEndSource = file_get_contents(resource_path('views/components/public-discovery/page-end.blade.php'));
    $footerSource = file_get_contents(resource_path('views/components/public-discovery/footer.blade.php'));

    expect($pageEndSource)
        ->toContain('data-public-discovery-page-end')
        ->toContain('<x-public-discovery.footer />')
        ->toContain('<x-public-discovery.chatbot />');

    expect($footerSource)->not->toContain('<x-public-discovery.chatbot />');
});

it('uses the same Miftah public chrome on every guest authentication page', function () {
    foreach ([
        resource_path('views/auth/login.blade.php'),
        resource_path('views/auth/forgot-password.blade.php'),
        resource_path('views/auth/reset-password.blade.php'),
    ] as $viewPath) {
        $source = file_get_contents($viewPath);

        expect($source)
            ->toContain(':show-navbar="false"')
            ->toContain(':show-footer="false"')
            ->toContain(':show-chatbot="false"')
            ->toContain('<x-public-discovery.navbar')
            ->toContain('<x-public-discovery.page-end />')
            ->not->toContain('<x-site.navbar')
            ->not->toContain('<x-site.footer')
            ->not->toContain('<x-site.chatbot');
    }

    foreach ([
        route('auth.login'),
        route('auth.password.request'),
        route('auth.password.reset', ['token' => 'test-token', 'email' => 'student@example.com']),
    ] as $url) {
        $content = $this->get($url)
            ->assertOk()
            ->assertSee('data-public-discovery-navbar', false)
            ->assertSee('data-public-discovery-page-end', false)
            ->assertSee('data-public-discovery-footer', false)
            ->assertSee('data-chatbot-widget', false)
            ->getContent();

        expect(substr_count($content, 'data-public-discovery-navbar'))->toBe(1);
        expect(substr_count($content, 'data-public-discovery-footer'))->toBe(1);
        expect(substr_count($content, 'data-chatbot-widget'))->toBe(1);
    }
});

it('renders the refined home discovery sections from the Stitch reference', function () {
    miftahSprint1Program();

    foreach ([
        ['name' => 'Andi Darmawan', 'slug' => 'andi-darmawan'],
        ['name' => 'Sarah Nabila', 'slug' => 'sarah-nabila'],
        ['name' => 'Ibu Budi', 'slug' => 'ibu-budi'],
    ] as $index => $testimonial) {
        Content::query()->create([
            'type' => 'testimonial',
            'title' => $testimonial['name'],
            'slug' => $testimonial['slug'],
            'body' => 'Cerita pengalaman belajar bersama ETC Planet.',
            'meta' => ['role' => 'Siswa ETC Planet', 'rating' => 5],
            'display_order' => $index + 1,
            'is_published' => true,
        ]);
    }

    Content::query()->create([
        'type' => 'faq',
        'title' => 'Bagaimana cara mendaftar?',
        'slug' => 'cara-mendaftar-home',
        'body' => 'Pilih program lalu lengkapi formulir pendaftaran.',
        'display_order' => 1,
        'is_published' => true,
    ]);

    $this->get(route('public.home'))
        ->assertOk()
        ->assertSee('public-home-stats', false)
        ->assertSee('data-public-stat-counter', false)
        ->assertSee('data-counter-target', false)
        ->assertSee('public-home-program-card__heading', false)
        ->assertSee('public-home-program-card__pricing', false)
        ->assertSee('public-home-program-card__actions', false)
        ->assertSee('data-home-program-grid', false)
        ->assertSee('Lihat Semua Program')
        ->assertSee('data-public-carousel', false)
        ->assertSee('data-carousel-viewport', false)
        ->assertSee('data-carousel-slide', false)
        ->assertSee('data-carousel-prev', false)
        ->assertSee('data-carousel-next', false)
        ->assertSee('public-registration-flow', false)
        ->assertSee('data-registration-flow-step="1"', false)
        ->assertSee('data-registration-flow-step="5"', false)
        ->assertSee('data-public-testimonials', false)
        ->assertSee('Apa Kata Mereka?')
        ->assertSee('Andi Darmawan')
        ->assertSee('Sarah Nabila')
        ->assertSee('Ibu Budi')
        ->assertSee('Mulai Pendaftaran')
        ->assertSee(route('public.team.index'), false)
        ->assertSee('Cuplikan suasana belajar')
        ->assertSee('data-home-faq', false)
        ->assertSee('Pertanyaan yang sering ditanyakan')
        ->assertSee('home-faq-answer-0', false)
        ->assertSee('Lihat Semua FAQ')
        ->assertSee(route('public.faq.index'), false)
        ->assertSee('data-chatbot-suggestion', false)
        ->assertSee('ETC Planet Bot')
        ->assertSee('Program yang tersedia')
        ->assertDontSee('ETC Planet Assistant');

    $scriptSource = file_get_contents(resource_path('js/app.js'));
    $homeSource = file_get_contents(resource_path('views/pages/public/home.blade.php'));
    $styleSource = file_get_contents(resource_path('css/app.css'));

    expect($scriptSource)
        ->toContain("const storageKey = 'etc-registration-progress'")
        ->toContain("flow.style.setProperty('--registration-progress'")
        ->toContain("step.classList.toggle('is-complete', completed)")
        ->toContain('function initPublicStatCounters()')
        ->toContain("document.querySelectorAll('[data-public-stat-counter]')")
        ->toContain('observer.unobserve(entry.target)')
        ->toContain('initPublicStatCounters()')
        ->toContain('initPublicHomeCarousels()')
        ->toContain("viewport.addEventListener('pointermove'")
        ->toContain('resetLoopPosition')
        ->toContain("viewport.style.scrollBehavior = 'auto'")
        ->toContain('window.setInterval');

    expect($homeSource)
        ->toContain('mx-auto max-w-2xl text-center')
        ->toContain('Partner belajar dan pengembangan bahasa')
        ->toContain('$programs->take(6)')
        ->toContain('heroicon-m-chevron-left')
        ->toContain('heroicon-m-chevron-right')
        ->toContain("route('public.reels.index', ['reel' => \$reel->getKey()])")
        ->toContain('public-home-carousel--four')
        ->toContain('public-home-instructor-card')
        ->not->toContain('color="primary" class="public-home-carousel__arrow"')
        ->not->toContain('data-carousel-status')
        ->not->toContain('md:flex-row md:items-end public-reveal');

    expect($styleSource)
        ->toContain('inset: 0 -3.75rem 1.25rem')
        ->toContain('.public-home-carousel__arrow[data-carousel-prev]')
        ->toContain('.public-home-carousel__arrow[data-carousel-next]')
        ->toContain('.public-testimonial-card:hover')
        ->toContain('.public-home-partner-card:hover')
        ->toContain('.public-home-reel-card:hover')
        ->toContain('.public-home-instructor-card')
        ->toContain('.public-home-instructor-card img')
        ->toContain('background: transparent')
        ->toContain('0 10px 24px rgb(58 44 51 / 4%)')
        ->toContain('pointer-events: none');
});

it('keeps the public discovery chatbot local and interactive without editing shared controls', function () {
    $chatbotSource = file_get_contents(resource_path('views/components/public-discovery/chatbot.blade.php'));
    $scriptSource = file_get_contents(resource_path('js/app.js'));

    expect($chatbotSource)
        ->toContain('data-chatbot-widget')
        ->toContain('data-chatbot-suggestion')
        ->toContain('<x-ui.field')
        ->toContain('<x-ui.icon-button')
        ->not->toContain('public-discovery-chatbot__notification')
        ->not->toContain('public-discovery-chatbot__status-dot')
        ->not->toMatch('/<(button|input|select|textarea)\b/i');

    expect($scriptSource)
        ->toContain("widget.querySelectorAll('[data-chatbot-suggestion]')")
        ->toContain('public-discovery-chatbot__bubble--user')
        ->toContain('public-discovery-chatbot__bubble--bot')
        ->toContain('showTypingIndicator')
        ->not->toContain("'--public-chatbot-footer-offset'")
        ->toContain("widget.classList.toggle('is-open', isOpen)");
});

it('renders FAQ as a clean collapsed accordion with arrow controls', function () {
    Content::query()->create([
        'type' => 'faq',
        'title' => 'Apakah placement test dilakukan secara online?',
        'slug' => 'placement-test-offline',
        'body' => 'Tidak. Placement test dilaksanakan secara offline di ETC Planet.',
        'display_order' => 1,
        'is_published' => true,
    ]);

    $this->get(route('public.faq.index'))
        ->assertOk()
        ->assertSee('data-public-faq', false)
        ->assertSee('data-faq-toggle', false)
        ->assertSee('data-faq-answer', false)
        ->assertSee('expand_more')
        ->assertSee('aria-expanded="false"', false)
        ->assertDontSee('>add<', false)
        ->assertDontSee('public-card p-5', false);

    $scriptSource = file_get_contents(resource_path('js/app.js'));

    expect($scriptSource)
        ->toContain('function initPublicFaq()')
        ->toContain('setOpen(item, opening)')
        ->not->toContain('items.forEach((candidate) => setOpen(candidate, candidate === item && opening))');
});

it('keeps reel playback feedback brief and provides a temporary mute toggle', function () {
    $source = file_get_contents(resource_path('js/app.js'));

    expect($source)
        ->toContain("video?.addEventListener('click'")
        ->toContain("event.target.closest('[data-reel-sound-control], [data-reel-like]')")
        ->toContain("control.classList.add('is-visible')")
        ->toContain('}, 1100));')
        ->toContain("window.localStorage.setItem('etc-reels-muted'")
        ->toContain('}, 240));')
        ->not->toContain("window.localStorage.setItem('etc-reels-volume'")
        ->not->toContain("showPlaybackIndicator(video, 'play_arrow', true)");
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
        'views/pages/public/faq/index.blade.php',
        'views/pages/public/programs/index.blade.php',
        'views/pages/public/programs/show.blade.php',
        'views/pages/public/reels/index.blade.php',
        'views/public/registration/create.blade.php',
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
    ] as $file) {
        expect(file_get_contents(resource_path($file)))->not->toMatch('/<a\b/i');
    }

    foreach ([
        'views/pages/public/about.blade.php',
        'views/pages/public/facilities/index.blade.php',
        'views/pages/public/gallery/index.blade.php',
        'views/pages/public/home.blade.php',
        'views/pages/public/programs/index.blade.php',
        'views/pages/public/programs/show.blade.php',
    ] as $file) {
        expect(file_get_contents(resource_path($file)))
            ->not->toContain('public-chip')
            ->not->toContain('public-link');
    }
});
