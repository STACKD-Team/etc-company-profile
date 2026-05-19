<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RegistrationProgramController extends Controller
{
    public function index(Request $request): View
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
                'contact_url' => route('public.contact.index', ['program' => $program->id]),
            ]);
        $selectedProgram = $programs->firstWhere('id', (int) $request->query('program')) ?? $programs->first();

        return view('registration.programs.index', [
            'programs' => $programs,
            'selectedProgram' => $selectedProgram,
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
