<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InstructorController extends Controller
{
    public function index(Request $request): View
    {
        $allowedSorts = [
            'full_name' => 'full_name',
            'email' => 'email',
            'classes_taught_count' => 'classes_taught_count',
            'created_at' => 'created_at',
        ];
        $sort = $allowedSorts[$request->string('sort')->toString()] ?? 'full_name';
        $direction = $request->string('direction')->lower()->toString() === 'desc' ? 'desc' : 'asc';

        $instructors = User::query()
            ->instructors()
            ->withCount('classesTaught')
            ->when($request->string('search')->toString(), fn ($query, string $search) => $query
                ->where(fn ($query) => $query
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('full_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")))
            ->when($request->string('specialization')->toString(), fn ($query, string $specialization) => $query->where('instructor_specialization', 'like', "%{$specialization}%"))
            ->orderBy($sort, $direction)
            ->paginate(12)
            ->withQueryString();

        return view('pages.admin.instructor.index', compact('instructors'));
    }

    public function show(User $instructor): View
    {
        abort_unless($instructor->role === 'instructor', 404);

        return view('pages.admin.instructor.show', [
            'instructor' => $instructor->load(['classesTaught.program']),
        ]);
    }
}
