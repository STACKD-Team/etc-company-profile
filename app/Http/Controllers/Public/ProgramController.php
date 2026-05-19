<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Program;
use Illuminate\View\View;

class ProgramController extends Controller
{
    public function show(Program $program): View
    {
        abort_unless($program->is_active, 404);

        $featuredClass = $program->classes()
            ->with('instructor')
            ->whereIn('status', ['ongoing', 'upcoming'])
            ->orderByRaw('CASE WHEN start_date IS NULL THEN 1 ELSE 0 END')
            ->orderBy('start_date')
            ->first();

        return view('public.programs.show', [
            'program' => $program,
            'detailContent' => config('program_details.programs.'.$program->slug, config('program_details.default')),
            'featuredClass' => $featuredClass,
            'featuredInstructor' => $featuredClass?->instructor,
        ]);
    }
}
