<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Program;
use Illuminate\View\View;

class RegistrationProgramController extends Controller
{
    public function index(): View
    {
        $programs = Program::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(fn (Program $program): array => [
                'id' => $program->id,
                'name' => $program->name,
                'description' => $program->description ?: 'Program belajar ETC Planet yang dirancang untuk kebutuhan siswa.',
                'icon' => $this->iconFor($program),
                'tone' => $program->category === 'english' ? 'icon-pink' : 'icon-dark',
                'registration_fee' => (int) $program->registration_fee,
                'program_price' => (int) $program->price,
                'duration_meetings' => $program->duration_meetings,
            ]);

        return view('registration.programs.index', [
            'programs' => $programs,
            'selectedProgram' => $programs->first(),
        ]);
    }

    private function iconFor(Program $program): string
    {
        $name = str($program->name)->lower()->toString();

        return match (true) {
            str_contains($name, 'toefl') || $program->category === 'test_prep' => 'toefl-preparation',
            str_contains($name, 'jepang') || str_contains($name, 'korea') || $program->category === 'japanese' || $program->category === 'mandarin' => 'bahasa-asia',
            str_contains($name, 'kids') || $program->target_age === 'kids' => 'kids-english',
            default => 'general-english',
        };
    }
}
