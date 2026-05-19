<?php

namespace Database\Seeders;

use App\Models\CourseClass;
use App\Models\Program;
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
                'is_active' => true,
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

        CourseClass::query()->updateOrCreate(
            [
                'program_id' => $englishConversation->id,
                'name' => 'English Conversation Regular',
            ],
            [
                'instructor_id' => $englishInstructor->id,
                'schedule_days' => 'Senin - Kamis',
                'schedule_time' => '16:00 - 17:30 WIB',
                'room' => 'ETC Planet Room 1',
                'start_date' => now()->addWeek()->toDateString(),
                'status' => 'upcoming',
            ],
        );
    }
}
