<?php

namespace Database\Seeders;

use App\Models\CourseClass;
use App\Models\Program;
use App\Models\ProgramPromotion;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            StudentPanelSeeder::class,
        ]);

        User::query()->updateOrCreate(
            ['email' => 'admin@etcplanet.test'],
            [
                'name' => 'Admin ETC Planet',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_active' => true,
            ],
        );

        $englishConversation = Program::query()->updateOrCreate(
            ['slug' => 'english-conversation'],
            [
                'name' => 'English Conversation',
                'category' => 'english',
                'type' => 'regular',
                'target_age' => 'all',
                'description' => 'Program interaktif untuk meningkatkan kemampuan speaking, listening, pronunciation, dan kosakata bahasa Inggris dalam situasi sehari-hari maupun profesional.',
                'duration_meetings' => 16,
                'max_students' => 10,
                'price' => 1200000,
                'registration_fee' => 200000,
                'thumbnail' => 'images/pu1-img.jpg',
                'is_active' => true,
            ],
        );

        ProgramPromotion::query()->updateOrCreate(
            [
                'program_id' => $englishConversation->id,
                'title' => 'Early Bird Conversation',
            ],
            [
                'description' => 'Potongan khusus untuk pendaftaran awal kelas conversation.',
                'discount_type' => 'fixed',
                'discount_value' => 150000,
                'starts_at' => now()->subWeek(),
                'ends_at' => now()->addMonth(),
                'is_active' => true,
                'badge_label' => 'Early Bird',
                'terms' => 'Berlaku untuk kuota pendaftaran awal dan tidak dapat digabung dengan promo lain.',
            ],
        );

        $englishInstructor = User::query()->updateOrCreate(
            ['email' => 'sarah.amalia@etcplanet.test'],
            [
                'name' => 'Sarah Amalia, M.Pd.',
                'full_name' => 'Sarah Amalia, M.Pd.',
                'password' => Hash::make('password'),
                'role' => 'instructor',
                'is_active' => true,
                'instructor_position' => 'Instructor Utama',
                'instructor_specialization' => 'English Conversation',
                'instructor_bio' => 'Instructor bahasa Inggris yang fokus pada speaking, pronunciation, dan kelas interaktif.',
                'show_on_team_page' => true,
            ],
        );

        $room = Room::query()->updateOrCreate(
            ['name' => 'ETC Planet Room 1'],
            [
                'description' => 'Room utama untuk kelas conversation reguler.',
                'capacity' => 12,
                'facilities' => ['AC', 'Whiteboard', 'Projector'],
                'is_active' => true,
                'display_order' => 1,
            ],
        );

        CourseClass::query()->updateOrCreate(
            [
                'program_id' => $englishConversation->id,
                'name' => 'English Conversation Regular',
            ],
            [
                'instructor_id' => $englishInstructor->id,
                'schedule_days' => 'Senin - Kamis',
                'schedule_time' => '16:00 - 17:30 WIB',
                'room_id' => $room->id,
                'start_date' => now()->addWeek()->toDateString(),
                'status' => 'upcoming',
            ],
        );
    }
}
