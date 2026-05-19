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
            ->when($request->string('category')->toString(), fn ($query, string $category) => $query->where('category', $category))
            ->orderBy('name')
            ->get();

        return view('public.programs.index', [
            'programs' => $programs,
            'categories' => Program::query()->where('is_active', true)->distinct()->pluck('category')->filter()->values(),
            'selectedCategory' => $request->string('category')->toString(),
        ]);
    }
}
