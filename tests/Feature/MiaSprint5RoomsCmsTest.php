<?php

use App\Filament\Resources\Rooms\RoomResource;
use App\Models\Content;
use App\Models\CourseClass;
use App\Models\Program;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;

uses(RefreshDatabase::class);

it('aligns Sprint 5 room schema and admin class fields to room_id', function () {
    expect(Schema::hasTable('rooms'))->toBeTrue()
        ->and(Schema::hasColumn('classes', 'room_id'))->toBeTrue()
        ->and(Schema::hasColumn('classes', 'room'))->toBeFalse();

    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    $program = Program::query()->create([
        'name' => 'Sprint 5 Program',
        'slug' => 'sprint-5-program',
        'category' => 'english',
    ]);
    $room = Room::query()->create(['name' => 'Sprint 5 Room', 'is_active' => true]);

    $this->actingAs($admin)
        ->get(route('admin.class.create'))
        ->assertOk()
        ->assertSee('name="room_id"', false)
        ->assertDontSee('name="room"', false);

    $this->actingAs($admin)
        ->post(route('admin.class.store'), [
            'program_id' => $program->id,
            'room_id' => $room->id,
            'name' => 'Room Linked Sprint 5 Class',
            'status' => 'upcoming',
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect();

    $class = CourseClass::query()->where('name', 'Room Linked Sprint 5 Class')->firstOrFail();

    expect($class->room_id)->toBe($room->id)
        ->and($class->room_label)->toBe('Sprint 5 Room');
});

it('lets Mia manage rooms and exposes only active rooms publicly', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

    $this->actingAs($admin)
        ->post(route('admin.room.store'), [
            'name' => 'Mia Active Room',
            'description' => 'Room aktif dari Sprint 5.',
            'capacity' => 14,
            'facilities_text' => "AC\nProjector",
            'is_active' => '1',
            'display_order' => 1,
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect();

    $inactive = Room::query()->create([
        'name' => 'Mia Hidden Room',
        'description' => 'Tidak tampil public.',
        'is_active' => false,
    ]);

    $active = Room::query()->where('name', 'Mia Active Room')->firstOrFail();

    $this->actingAs($admin)
        ->put(route('admin.room.update', $active), [
            'name' => 'Mia Active Room Updated',
            'description' => 'Room aktif yang sudah diperbarui.',
            'capacity' => 16,
            'facilities_text' => "AC\nWhiteboard",
            'is_active' => '1',
            'display_order' => 2,
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('admin.room.show', $active));

    expect($active->refresh()->capacity)->toBe(16)
        ->and($active->facilities)->toBe(['AC', 'Whiteboard']);

    $this->get(route('public.facilities.index'))
        ->assertOk()
        ->assertSee('Mia Active Room Updated')
        ->assertDontSee($inactive->name);
});

it('stores CMS content with route-derived type, generated slug, and whitelisted metadata', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

    $this->actingAs($admin)
        ->post(route('admin.gallery.store'), [
            'type' => Content::TYPE_FAQ,
            'title' => 'Mia Gallery Sprint 5',
            'slug' => 'custom-slug-ignored',
            'body' => 'Dokumentasi gallery Sprint 5.',
            'display_order' => 3,
            'is_published' => '1',
            'meta' => [
                'caption' => 'Kelas speaking',
                'location' => 'Padang',
                'role' => 'ignored for gallery',
            ],
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect();

    $gallery = Content::query()->where('title', 'Mia Gallery Sprint 5')->firstOrFail();

    expect($gallery->type)->toBe(Content::TYPE_GALLERY)
        ->and($gallery->slug)->toBe('mia-gallery-sprint-5')
        ->and($gallery->meta)->toBe([
            'caption' => 'Kelas speaking',
            'location' => 'Padang',
        ]);
});

it('keeps FAQ simple and validates testimonial rating from one to five', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

    $this->actingAs($admin)
        ->get(route('admin.faq.create'))
        ->assertOk()
        ->assertSee('Pertanyaan')
        ->assertSee('Jawaban')
        ->assertDontSee('Slug')
        ->assertDontSee('Gambar Utama');

    $this->actingAs($admin)
        ->post(route('admin.faq.store'), [
            'title' => 'Apakah placement test online?',
            'body' => 'Placement test tetap offline.',
            'meta' => ['role' => 'ignored'],
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect();

    $faq = Content::query()->where('title', 'Apakah placement test online?')->firstOrFail();

    expect($faq->type)->toBe(Content::TYPE_FAQ)
        ->and($faq->meta)->toBe([]);

    $this->actingAs($admin)
        ->post(route('admin.testimonial.store'), [
            'title' => 'Orang Tua Siswa',
            'body' => 'Anak saya lebih percaya diri.',
            'meta' => ['role' => 'Parent', 'rating' => 6],
        ])
        ->assertSessionHasErrors(['meta.rating']);

    $this->actingAs($admin)
        ->post(route('admin.testimonial.store'), [
            'title' => 'Orang Tua Siswa',
            'body' => 'Anak saya lebih percaya diri.',
            'meta' => ['role' => 'Parent', 'rating' => 5],
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect();

    $testimonial = Content::query()->where('type', Content::TYPE_TESTIMONIAL)->where('title', 'Orang Tua Siswa')->firstOrFail();

    expect($testimonial->meta)->toBe(['role' => 'Parent', 'rating' => 5]);
});

it('keeps CMS admin surfaces friendly by hiding slug, redundant type, and raw metadata', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

    $gallery = Content::query()->create([
        'type' => Content::TYPE_GALLERY,
        'title' => 'Polished Gallery Sprint 5',
        'slug' => 'polished-gallery-sprint-5',
        'body' => 'Ringkasan kegiatan speaking class.',
        'meta' => ['caption' => 'Hidden internal caption', 'location' => 'Padang'],
        'is_published' => true,
    ]);

    $partner = Content::query()->create([
        'type' => Content::TYPE_PARTNER,
        'title' => 'Polished Partner Sprint 5',
        'slug' => 'polished-partner-sprint-5',
        'body' => 'Partner sekolah aktif.',
        'meta' => ['category' => 'Sekolah', 'website' => 'https://partner.example.test', 'since' => '2025'],
        'is_published' => true,
    ]);

    $testimonial = Content::query()->create([
        'type' => Content::TYPE_TESTIMONIAL,
        'title' => 'Polished Testimonial Sprint 5',
        'slug' => 'polished-testimonial-sprint-5',
        'body' => 'Anak saya lebih percaya diri.',
        'meta' => ['role' => 'Orang tua siswa', 'rating' => 5],
        'is_published' => true,
    ]);

    $this->actingAs($admin)
        ->get(route('admin.gallery.index'))
        ->assertOk()
        ->assertSee('Cari judul atau deskripsi')
        ->assertSee('Ringkasan kegiatan speaking class.')
        ->assertDontSee('Cari judul atau slug')
        ->assertDontSee('polished-gallery-sprint-5')
        ->assertDontSee('Tipe');

    $this->actingAs($admin)
        ->get(route('admin.gallery.show', $gallery))
        ->assertOk()
        ->assertSee('Detail Gallery')
        ->assertDontSee('Slug')
        ->assertDontSee('slug')
        ->assertDontSee('Tipe')
        ->assertDontSee('Hidden internal caption')
        ->assertDontSee('Location');

    $this->actingAs($admin)
        ->get(route('admin.partner.show', $partner))
        ->assertOk()
        ->assertSee('Kategori')
        ->assertSee('Website')
        ->assertSee('Tahun kerja sama')
        ->assertSee('data-open-modal="delete-action-', false)
        ->assertSee('role="dialog"', false)
        ->assertDontSee('polished-partner-sprint-5')
        ->assertDontSee('category');

    $this->actingAs($admin)
        ->get(route('admin.testimonial.show', $testimonial))
        ->assertOk()
        ->assertSee('Role / asal')
        ->assertSee('Rating')
        ->assertSee('5/5')
        ->assertDontSee('polished-testimonial-sprint-5');
});

it('rejects legacy CMS type submissions and aligns Filament resources with Sprint 5 fields', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

    $this->actingAs($admin)
        ->post(route('admin.gallery.store'), [
            'type' => 'room',
            'title' => 'Invalid Room Content',
        ])
        ->assertSessionHasErrors(['type']);

    expect(class_exists(RoomResource::class))->toBeTrue();

    $classForm = file_get_contents(app_path('Filament/Resources/CourseClasses/Schemas/CourseClassForm.php'));
    $contentForm = file_get_contents(app_path('Filament/Resources/Contents/Schemas/ContentForm.php'));
    $contentTable = file_get_contents(app_path('Filament/Resources/Contents/Tables/ContentsTable.php'));

    expect($classForm)->toContain("Select::make('room_id')")
        ->and($classForm)->not->toContain("TextInput::make('room')")
        ->and($contentForm)->not->toContain("'room' => 'Room'")
        ->and($contentForm)->not->toContain('team_member_extra')
        ->and($contentForm)->not->toContain("Textarea::make('meta')")
        ->and($contentTable)->not->toContain("TextColumn::make('type')")
        ->and($contentTable)->not->toContain("'setting' => 'Setting'");
});
