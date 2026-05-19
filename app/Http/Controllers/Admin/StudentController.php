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
        $students = User::query()
            ->students()
            ->withCount('enrollments')
            ->when($request->string('search')->toString(), fn ($query, string $search) => $query
                ->where(fn ($query) => $query
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('full_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")))
            ->orderBy('full_name')
            ->paginate(12)
            ->withQueryString();

        return view('admin.students.index', compact('students'));
    }

    public function show(User $student): View
    {
        abort_unless($student->role === 'student', 404);

        return view('admin.students.show', [
            'student' => $student->load(['enrollments.courseClass.program', 'registrations.program']),
        ]);
    }
}
