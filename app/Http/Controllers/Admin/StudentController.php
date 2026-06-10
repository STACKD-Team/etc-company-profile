<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function index(Request $request): View
    {
        $allowedSorts = [
            'full_name' => 'full_name',
            'email' => 'email',
            'status' => 'status',
            'enrollments_count' => 'enrollments_count',
            'created_at' => 'created_at',
        ];
        $sort = $allowedSorts[$request->string('sort')->toString()] ?? 'full_name';
        $direction = $request->string('direction')->lower()->toString() === 'desc' ? 'desc' : 'asc';

        $students = User::query()
            ->students()
            ->withCount('enrollments')
            ->when($request->string('search')->toString(), fn ($query, string $search) => $query
                ->where(fn ($query) => $query
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('full_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('no_induk', 'like', "%{$search}%")))
            ->when($request->string('status')->toString(), fn ($query, string $status) => $query->where('status', 'like', "%{$status}%"))
            ->orderBy($sort, $direction)
            ->paginate(12)
            ->withQueryString();

        return view('admin.students.index', compact('students'));
    }

    public function show(User $student): View
    {
        abort_unless($student->role === 'student', 404);

        return view('admin.students.show', [
            'student' => $student->load(['enrollments.courseClass.program', 'enrollments.courseClass.instructor', 'enrollments.reportCard', 'registrations.program']),
        ]);
    }
}
