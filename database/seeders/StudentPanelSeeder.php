<?php

namespace Database\Seeders;

use App\Models\CourseClass;
use App\Models\Enrollment;
use App\Models\Program;
use App\Models\Registration;
use App\Models\ReportCard;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StudentPanelSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()->updateOrCreate(
            ['email' => 'admin@etcplanet.test'],
            [
                'name' => 'Admin ETC Planet',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_active' => true,
            ],
        );

        $instructor = User::query()->updateOrCreate(
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

        $student = User::query()->updateOrCreate(
            ['email' => 'student@etcplanet.test'],
            [
                'name' => 'Mecca Student',
                'full_name' => 'Mecca Student Demo',
                'password' => Hash::make('password'),
                'role' => 'student',
                'is_active' => true,
                'no_induk' => 'ETC-STU-0001',
                'place_of_birth' => 'Padang',
                'date_of_birth' => '2010-06-09',
                'sex' => 'F',
                'religion' => 'Islam',
                'nationality' => 'Indonesia',
                'status' => 'Pelajar',
                'occupation_school' => 'SMP Negeri 1 Padang',
                'mobile_phone' => '081234567890',
                'nisn' => '0012345678',
                'nik' => '1371000000000001',
                'address' => 'Jl. S. Parman No. 202B, Padang',
                'rt_rw' => '001/002',
                'postal_code' => '25134',
                'village' => 'Ulak Karang Selatan',
                'sub_district' => 'Padang Utara',
                'district' => 'Padang',
                'province' => 'Sumatera Barat',
                'living_with' => 'Orang Tua',
                'transportation' => 'Diantar Orang Tua',
                'mother_name' => 'Ibu Mecca',
                'father_name' => 'Ayah Mecca',
            ],
        );

        $program = Program::query()->updateOrCreate(
            ['slug' => 'english-conversation-teen-demo'],
            [
                'name' => 'English Conversation Teen Demo',
                'category' => 'english',
                'type' => 'regular',
                'target_age' => 'teen',
                'description' => 'Program demo untuk dashboard siswa ETC Planet dengan fokus speaking, listening, pronunciation, dan vocabulary.',
                'duration_meetings' => 16,
                'max_students' => 10,
                'price' => 1200000,
                'registration_fee' => 200000,
                'is_active' => true,
            ],
        );

        $hardRockRoom = Room::query()->updateOrCreate(
            ['name' => 'Hard Rock'],
            [
                'description' => 'Room aktif untuk kelas teen speaking.',
                'capacity' => 12,
                'facilities' => ['AC', 'Whiteboard', 'Speaking cards'],
                'is_active' => true,
                'display_order' => 1,
            ],
        );

        $disneylandRoom = Room::query()->updateOrCreate(
            ['name' => 'Disneyland'],
            [
                'description' => 'Room untuk kelas lanjutan dan aktivitas bahasa.',
                'capacity' => 10,
                'facilities' => ['AC', 'Learning props', 'Whiteboard'],
                'is_active' => true,
                'display_order' => 2,
            ],
        );

        $activeClass = CourseClass::query()->updateOrCreate(
            [
                'program_id' => $program->id,
                'name' => 'Teen 4 Demo',
            ],
            [
                'instructor_id' => $instructor->id,
                'schedule_days' => 'Tues-Thurs',
                'schedule_time' => '17.00-18.30',
                'room_id' => $hardRockRoom->id,
                'start_date' => now()->subWeeks(3)->toDateString(),
                'end_date' => now()->addWeeks(5)->toDateString(),
                'status' => 'ongoing',
            ],
        );

        $completedClass = CourseClass::query()->updateOrCreate(
            [
                'program_id' => $program->id,
                'name' => 'Teen 3 Demo',
            ],
            [
                'instructor_id' => $instructor->id,
                'schedule_days' => 'Mon-Wed',
                'schedule_time' => '15.00-16.30',
                'room_id' => $disneylandRoom->id,
                'start_date' => now()->subMonths(4)->toDateString(),
                'end_date' => now()->subMonth()->toDateString(),
                'status' => 'completed',
            ],
        );

        Registration::query()->updateOrCreate(
            ['registration_code' => 'REG-STUDENT-DEMO-001'],
            [
                'user_id' => $student->id,
                'program_id' => $program->id,
                'class_id' => $activeClass->id,
                'applicant_name' => $student->full_name,
                'applicant_email' => $student->email,
                'applicant_phone' => $student->mobile_phone,
                'preferred_days' => 'tue_thu',
                'preferred_time' => '17.00-18.30',
                'placement_test_at' => now()->subWeeks(4)->setTime(10, 0),
                'placement_test_result' => 'Student cocok masuk Teen 4.',
                'payment_method' => 'qris',
                'payment_amount' => 1400000,
                'payment_gateway_id' => 'DEMO-MIDTRANS-001',
                'paid_at' => now()->subWeeks(4),
                'status' => 'enrolled',
                'notes' => 'Data demo student panel.',
            ],
        );

        $activeEnrollment = Enrollment::query()->updateOrCreate(
            [
                'user_id' => $student->id,
                'class_id' => $activeClass->id,
            ],
            [
                'enrolled_at' => now()->subWeeks(3)->toDateString(),
                'completed_at' => null,
                'status' => 'active',
            ],
        );

        $completedEnrollment = Enrollment::query()->updateOrCreate(
            [
                'user_id' => $student->id,
                'class_id' => $completedClass->id,
            ],
            [
                'enrolled_at' => now()->subMonths(4)->toDateString(),
                'completed_at' => now()->subMonth()->toDateString(),
                'status' => 'completed',
            ],
        );

        ReportCard::query()->updateOrCreate(
            ['enrollment_id' => $completedEnrollment->id],
            [
                'score_listening' => 18,
                'score_vocabulary' => 17,
                'score_structure' => 16,
                'score_reading' => 18,
                'score_writing' => 17,
                'grade_pronunciation' => 'A',
                'grade_sentence_arrangement' => 'B',
                'grade_class_participation' => 'A',
                'grade_questioning_skill' => 'B',
                'grade_analyzing_skill' => 'A',
                'total_score' => 86,
                'final_grade' => 'A',
                'next_class' => 'Teen 4 Demo',
                'comments' => 'Progress belajar sangat baik. Siswa aktif bertanya dan percaya diri saat speaking practice.',
                'instructor_id' => $instructor->id,
                'academic_director_id' => $admin->id,
                'managing_director_id' => $admin->id,
                'issued_at' => now()->subWeeks(3)->toDateString(),
                'pdf_path' => null,
                'is_published' => true,
            ],
        );

        ReportCard::query()->updateOrCreate(
            ['enrollment_id' => $activeEnrollment->id],
            [
                'score_listening' => 16,
                'score_vocabulary' => 15,
                'score_structure' => 15,
                'score_reading' => 16,
                'score_writing' => 15,
                'grade_pronunciation' => 'B',
                'grade_sentence_arrangement' => 'B',
                'grade_class_participation' => 'A',
                'grade_questioning_skill' => 'B',
                'grade_analyzing_skill' => 'B',
                'total_score' => 77,
                'final_grade' => 'B',
                'next_class' => 'Teen 5',
                'comments' => 'Draft rapor demo untuk kelas aktif. Belum ditampilkan ke siswa karena belum dipublish.',
                'instructor_id' => $instructor->id,
                'academic_director_id' => $admin->id,
                'managing_director_id' => $admin->id,
                'issued_at' => null,
                'pdf_path' => null,
                'is_published' => false,
            ],
        );
    }
}
