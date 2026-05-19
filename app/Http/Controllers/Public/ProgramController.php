<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProgramController extends Controller
{
    public function index(Request $request): View
    {
        $programs = Program::query()
            ->where('is_active', true)
            ->when($request->string('category')->toString(), fn($query, string $category) => $query->where('category', $category))
            ->orderBy('name')
            ->get();

        return view('public.programs.index', [
            'programs' => $programs,
            'categories' => Program::query()->where('is_active', true)->distinct()->pluck('category')->filter()->values(),
            'selectedCategory' => $request->string('category')->toString(),
        ]);
    }

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
            'detailContent' => config('program_details.programs.' . $program->slug, config('program_details.default')),
            'featuredClass' => $featuredClass,
            'featuredInstructor' => $featuredClass?->instructor,
        ]);
    }
}
