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
        $this->seedProfile();
        $this->seedFaqs();
        $this->seedRooms();
        $this->seedGallery();
        $this->seedPartners();
        $this->seedTestimonials();
        $this->seedInstructors();
        $this->seedReels();
    }

    protected function seedProfile(): void
    {
        Content::query()->updateOrCreate(
            ['type' => Content::TYPE_PROFILE, 'slug' => 'etc-profile'],
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
                    'address' => 'Jl. S. Parman No. 202B, Ulak Karang Selatan, Padang',
                    'phone' => '+62 812-0000-0000',
                    'whatsapp' => '+62 812-0000-0000',
                    'email' => 'hello@etcplanet.test',
                    'instagram' => 'https://www.instagram.com/etcplanet/',
                    'hours' => 'Senin-Sabtu, 09.00-18.30',
                    'map_url' => 'https://maps.google.com/?q=Jl.+S.+Parman+No.+202B+Padang',
                ],
                'display_order' => 1,
                'is_published' => true,
            ],
        );
    }

    protected function seedFaqs(): void
    {
        foreach ([
            [
                'slug' => 'cara-mendaftar',
                'question' => 'Bagaimana cara mendaftar di ETC Planet?',
                'answer' => 'Pilih program, isi formulir pendaftaran online, lalu ikuti instruksi pembayaran dan placement test.',
            ],
            [
                'slug' => 'placement-test',
                'question' => 'Apakah ada placement test?',
                'answer' => 'Ya. Placement test dilakukan offline agar siswa ditempatkan pada level kelas yang tepat.',
            ],
            [
                'slug' => 'biaya-pendaftaran',
                'question' => 'Berapa biaya pendaftaran?',
                'answer' => 'Biaya pendaftaran dan harga program dapat dilihat pada halaman program yang aktif.',
            ],
            [
                'slug' => 'request-jadwal',
                'question' => 'Apakah jadwal bisa request?',
                'answer' => 'Bisa. Calon siswa dapat menyampaikan preferensi jadwal saat mengisi formulir pendaftaran.',
            ],
        ] as $index => $faq) {
            Content::query()->updateOrCreate(
                ['type' => Content::TYPE_FAQ, 'slug' => $faq['slug']],
                [
                    'title' => $faq['question'],
                    'body' => $faq['answer'],
                    'meta' => [],
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
                'name' => 'Hard Rock Room',
                'description' => 'Ruang kelas energik untuk kelas conversation, teen, dan latihan speaking.',
                'image' => 'images/pu1-img.jpg',
                'capacity' => 12,
                'facilities' => ['AC', 'Projector', 'Whiteboard', 'Speaking cards'],
            ],
            [
                'name' => 'Disneyland Room',
                'description' => 'Ruang belajar hangat untuk kids class dan aktivitas bahasa yang lebih playful.',
                'image' => 'images/pu1-img (5).jpg',
                'capacity' => 10,
                'facilities' => ['AC', 'Learning props', 'Whiteboard', 'Reading corner'],
            ],
            [
                'name' => 'Louis Vuitton Room',
                'description' => 'Ruang fokus untuk kelas persiapan tes dan private coaching.',
                'image' => 'images/pu2-img.jpg',
                'capacity' => 8,
                'facilities' => ['AC', 'Projector', 'Audio system', 'Test prep setup'],
            ],
        ] as $index => $room) {
            Room::query()->updateOrCreate(
                ['name' => $room['name']],
                [
                    'description' => $room['description'],
                    'image' => $room['image'],
                    'capacity' => $room['capacity'],
                    'facilities' => $room['facilities'],
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

    protected function seedTestimonials(): void
    {
        foreach ([
            [
                'title' => 'Andi Darmawan',
                'slug' => 'testimoni-andi-darmawan',
                'body' => 'Skor TOEFL saya meningkat setelah mengikuti kelas intensif. Strategi menjawab soal dijelaskan dengan mudah dipahami.',
                'image' => null,
                'meta' => ['role' => 'Siswa TOEFL Preparation', 'rating' => 5],
            ],
            [
                'title' => 'Sarah Nabila',
                'slug' => 'testimoni-sarah-nabila',
                'body' => 'Metode belajarnya aktif dan banyak praktik sehingga saya lebih percaya diri berbicara bahasa Inggris.',
                'image' => 'images/profile_sarah.jpg',
                'meta' => ['role' => 'Siswa General English', 'rating' => 5],
            ],
            [
                'title' => 'Ibu Budi',
                'slug' => 'testimoni-ibu-budi',
                'body' => 'Pengajarnya sabar dan komunikatif. Perkembangan belajar anak juga disampaikan dengan jelas.',
                'image' => null,
                'meta' => ['role' => 'Orang Tua Siswa Kids Class', 'rating' => 4],
            ],
        ] as $index => $testimonial) {
            Content::query()->updateOrCreate(
                ['type' => Content::TYPE_TESTIMONIAL, 'slug' => $testimonial['slug']],
                [
                    ...$testimonial,
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
