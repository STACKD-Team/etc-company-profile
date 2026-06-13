<?php

namespace Database\Seeders;

use App\Models\Content;
use App\Models\Reel;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PublicDiscoverySeeder extends Seeder
{
    public function run(): void
    {
        $this->seedPages();
        $this->seedSettings();
        $this->seedRooms();
        $this->seedGallery();
        $this->seedPartners();
        $this->seedInstructors();
        $this->seedReels();
    }

    protected function seedPages(): void
    {
        Content::query()->updateOrCreate(
            ['type' => Content::TYPE_PROFILE, 'slug' => 'about'],
            [
                'title' => 'Tentang ETC Planet',
                'body' => "ETC Planet adalah lembaga kursus bahasa di Padang yang membantu siswa belajar dengan cara yang ramah, terarah, dan menyenangkan.\n\nKami menggabungkan kelas kecil, pengajar berpengalaman, dan placement test offline agar setiap siswa masuk ke level yang sesuai.",
                'image' => 'images/hero-img.jpeg',
                'meta' => [
                    'vision' => 'Menjadi pusat pembelajaran bahasa yang kredibel, hangat, dan relevan untuk kebutuhan akademik maupun profesional.',
                    'mission' => [
                        'Membantu siswa membangun kepercayaan diri berbahasa.',
                        'Menyediakan pembelajaran interaktif untuk anak, remaja, mahasiswa, dan profesional.',
                        'Menjaga proses belajar tetap terukur melalui kelas kecil dan evaluasi berkala.',
                    ],
                    'values' => ['Friendly', 'Focused', 'Practical', 'Progressive'],
                ],
                'display_order' => 1,
                'is_published' => true,
            ],
        );

        foreach ([
            ['question' => 'Bagaimana cara mendaftar di ETC Planet?', 'answer' => 'Untuk Sprint 1, calon siswa dapat mengirim pesan melalui form kontak. Tim ETC akan membantu memilih program, jadwal, dan tahap berikutnya.'],
            ['question' => 'Apakah ada placement test?', 'answer' => 'Ya. Placement test tetap dilakukan offline agar level kelas yang dipilih lebih tepat.'],
            ['question' => 'Berapa biaya pendaftaran?', 'answer' => 'Biaya pendaftaran awal adalah Rp 200.000. Biaya program menyesuaikan kelas yang dipilih.'],
            ['question' => 'Apakah jadwal bisa request?', 'answer' => 'Bisa. Calon siswa dapat menyampaikan preferensi jadwal saat konsultasi awal.'],
        ] as $index => $faq) {
            Content::query()->updateOrCreate(
                ['type' => Content::TYPE_FAQ, 'title' => $faq['question']],
                [
                    'slug' => 'faq-'.crc32($faq['question']),
                    'body' => $faq['answer'],
                    'meta' => [],
                    'display_order' => $index + 1,
                    'is_published' => true,
                ],
            );
        }
    }

    protected function seedSettings(): void
    {
        foreach ([
            ['title' => 'Address', 'slug' => 'address', 'value' => 'Jl. S. Parman No. 202B, Ulak Karang Selatan, Padang'],
            ['title' => 'Phone', 'slug' => 'phone', 'value' => '+62 812-0000-0000'],
            ['title' => 'Email', 'slug' => 'email', 'value' => 'hello@etcplanet.test'],
            ['title' => 'Instagram', 'slug' => 'instagram', 'value' => 'https://www.instagram.com/etcplanet/'],
            ['title' => 'Hours', 'slug' => 'hours', 'value' => 'Senin-Sabtu, 09.00-18.30'],
        ] as $index => $setting) {
            Content::query()->updateOrCreate(
                ['type' => Content::TYPE_PROFILE, 'slug' => $setting['slug']],
                [
                    'title' => $setting['title'],
                    'meta' => ['value' => $setting['value']],
                    'display_order' => $index + 1,
                    'is_published' => true,
                ],
            );
        }
    }

    protected function seedRooms(): void
    {
        foreach ([
            [
                'title' => 'Hard Rock Room',
                'slug' => 'hard-rock-room',
                'body' => 'Ruang kelas energik untuk kelas conversation, teen, dan latihan speaking.',
                'image' => 'images/pu1-img.jpg',
                'meta' => ['capacity' => 12, 'facility' => ['AC', 'Projector', 'Whiteboard', 'Speaking cards']],
            ],
            [
                'title' => 'Disneyland Room',
                'slug' => 'disneyland-room',
                'body' => 'Ruang belajar hangat untuk kids class dan aktivitas bahasa yang lebih playful.',
                'image' => 'images/pu1-img (5).jpg',
                'meta' => ['capacity' => 10, 'facility' => ['AC', 'Learning props', 'Whiteboard', 'Reading corner']],
            ],
            [
                'title' => 'Louis Vuitton Room',
                'slug' => 'louis-vuitton-room',
                'body' => 'Ruang fokus untuk kelas persiapan tes dan private coaching.',
                'image' => 'images/pu2-img.jpg',
                'meta' => ['capacity' => 8, 'facility' => ['AC', 'Projector', 'Audio system', 'Test prep setup']],
            ],
        ] as $index => $room) {
            Room::query()->updateOrCreate(
                ['name' => $room['title']],
                [
                    'description' => $room['body'],
                    'image' => $room['image'],
                    'capacity' => $room['meta']['capacity'] ?? null,
                    'facilities' => $room['meta']['facility'] ?? [],
                    'display_order' => $index + 1,
                    'is_active' => true,
                ],
            );
        }
    }

    protected function seedGallery(): void
    {
        foreach ([
            [
                'title' => 'Conversation Class Practice',
                'slug' => 'conversation-class-practice',
                'body' => 'Siswa berlatih speaking melalui roleplay dan simulasi percakapan sehari-hari.',
                'image' => 'images/pu1-img (3).jpg',
                'images' => ['images/pu1-img (3).jpg', 'images/pu1-img.jpg'],
                'meta' => ['event_date' => '2026-05-01', 'location' => 'ETC Planet Padang'],
            ],
            [
                'title' => 'Kids Class Activity',
                'slug' => 'kids-class-activity',
                'body' => 'Aktivitas kelas anak dengan games, flashcard, dan praktik kosakata.',
                'image' => 'images/pu1-img (5).jpg',
                'images' => ['images/pu1-img (5).jpg', 'images/hero-img.jpeg'],
                'meta' => ['event_date' => '2026-05-08', 'location' => 'ETC Planet Padang'],
            ],
            [
                'title' => 'TOEFL Strategy Session',
                'slug' => 'toefl-strategy-session',
                'body' => 'Sesi strategi mengerjakan soal TOEFL untuk kebutuhan akademik dan karier.',
                'image' => 'images/pu2-img.jpg',
                'images' => ['images/pu2-img.jpg', 'images/pu3-img.jpg'],
                'meta' => ['event_date' => '2026-05-12', 'location' => 'ETC Planet Padang'],
            ],
        ] as $index => $item) {
            Content::query()->updateOrCreate(
                ['type' => Content::TYPE_GALLERY, 'slug' => $item['slug']],
                [
                    'title' => $item['title'],
                    'body' => $item['body'],
                    'image' => $item['image'],
                    'images' => $item['images'],
                    'meta' => $item['meta'],
                    'display_order' => $index + 1,
                    'is_published' => true,
                ],
            );
        }
    }

    protected function seedPartners(): void
    {
        foreach ([
            [
                'title' => 'SMA Partner Padang',
                'slug' => 'sma-partner-padang',
                'body' => 'Kolaborasi kelas bahasa dan workshop speaking untuk siswa sekolah menengah.',
                'image' => 'images/hero-img.jpeg',
                'meta' => ['category' => 'Sekolah', 'since' => '2024', 'website' => 'https://etcplanet.test'],
            ],
            [
                'title' => 'Komunitas Bahasa Muda',
                'slug' => 'komunitas-bahasa-muda',
                'body' => 'Program latihan conversation untuk komunitas pelajar dan mahasiswa.',
                'image' => 'images/pu1-img.jpg',
                'meta' => ['category' => 'Komunitas', 'since' => '2025'],
            ],
            [
                'title' => 'Career Ready Center',
                'slug' => 'career-ready-center',
                'body' => 'Kerja sama persiapan interview, presentation skill, dan bahasa profesional.',
                'image' => 'images/pu2-img.jpg',
                'meta' => ['category' => 'Karier', 'since' => '2025'],
            ],
        ] as $index => $partner) {
            Content::query()->updateOrCreate(
                ['type' => Content::TYPE_PARTNER, 'slug' => $partner['slug']],
                [
                    'title' => $partner['title'],
                    'body' => $partner['body'],
                    'image' => $partner['image'],
                    'meta' => $partner['meta'],
                    'display_order' => $index + 1,
                    'is_published' => true,
                ],
            );
        }
    }

    protected function seedInstructors(): void
    {
        foreach ([
            [
                'name' => 'Ms. Debby',
                'email' => 'debby.instructor@etcplanet.test',
                'avatar' => 'images/Ms. Debby.jpeg',
                'position' => 'Senior English Tutor',
                'specialization' => 'TOEFL & IELTS',
                'bio' => 'Membantu siswa memahami strategi tes dan membangun akurasi grammar secara bertahap.',
            ],
            [
                'name' => 'Mr. Hafdi',
                'email' => 'hafdi.instructor@etcplanet.test',
                'avatar' => 'images/Mr. Hafdi.jpeg',
                'position' => 'Conversation Coach',
                'specialization' => 'Speaking Fluency',
                'bio' => 'Fokus pada latihan percakapan praktis agar siswa lebih percaya diri berbicara.',
            ],
            [
                'name' => 'Ms. Citra',
                'email' => 'citra.instructor@etcplanet.test',
                'avatar' => 'images/Ms. Citra.jpeg',
                'position' => 'Japanese Teacher',
                'specialization' => 'Japanese Basic',
                'bio' => 'Mengajar bahasa Jepang dasar dengan pendekatan visual dan latihan berulang.',
            ],
            [
                'name' => 'Ms. Rere',
                'email' => 'rere.instructor@etcplanet.test',
                'avatar' => 'images/Ms. Rere.jpeg',
                'position' => 'Kids Class Specialist',
                'specialization' => 'Fun Learning',
                'bio' => 'Membuat kelas anak terasa aktif, aman, dan penuh kesempatan praktik.',
            ],
        ] as $instructor) {
            User::query()->updateOrCreate(
                ['email' => $instructor['email']],
                [
                    'name' => $instructor['name'],
                    'password' => Hash::make('password'),
                    'role' => 'instructor',
                    'avatar' => $instructor['avatar'],
                    'is_active' => true,
                    'instructor_position' => $instructor['position'],
                    'instructor_specialization' => $instructor['specialization'],
                    'instructor_bio' => $instructor['bio'],
                    'show_on_team_page' => true,
                ],
            );
        }
    }

    protected function seedReels(): void
    {
        foreach ([
            [
                'title' => 'Roleplay Interview Kerja dalam Bahasa Inggris',
                'description' => 'Cuplikan latihan interview untuk membangun jawaban yang natural dan percaya diri.',
                'video_path' => 'videos/video1.mp4',
                'thumbnail_path' => 'images/pu1-img (3).jpg',
                'category' => 'edukasi',
                'views_count' => 1200,
                'likes_count' => 86,
            ],
            [
                'title' => 'Keseruan Kids Class Graduation',
                'description' => 'Momen akhir kelas anak dengan aktivitas ringan dan apresiasi untuk siswa.',
                'video_path' => 'videos/video2.mp4',
                'thumbnail_path' => 'images/pu1-img (5).jpg',
                'category' => 'dokumentasi',
                'views_count' => 3400,
                'likes_count' => 148,
            ],
        ] as $index => $reel) {
            Reel::query()->updateOrCreate(
                ['title' => $reel['title']],
                [
                    ...$reel,
                    'duration_seconds' => 45,
                    'is_published' => true,
                    'published_at' => now()->subDays(5 - $index),
                ],
            );
        }
    }
}
