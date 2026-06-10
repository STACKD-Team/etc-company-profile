<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCourseClassRequest;
use App\Models\CourseClass;
use App\Models\Program;
use App\Models\User;
use App\Services\CourseClassService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClassController extends Controller
{
    public function __construct(private CourseClassService $classService) {}

    public function index(Request $request): View
    {
        return view('admin.classes.index', [
            'classes' => $this->classService->paginate($request->only(['search', 'program_id', 'instructor_id', 'status', 'sort', 'direction']), 12),
            'programs' => Program::query()->orderBy('name')->get(),
            'instructors' => User::query()->instructors()->orderBy('full_name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.classes.create', $this->formData(new CourseClass()));
    }

    public function store(StoreCourseClassRequest $request): RedirectResponse
    {
        $this->classService->create($request->validated());

        return to_route('admin.classes.index')->with('status', 'Kelas berhasil dibuat.');
    }

    public function edit(CourseClass $class): View
    {
        return view('admin.classes.edit', $this->formData($class));
    }

    public function update(StoreCourseClassRequest $request, CourseClass $class): RedirectResponse
    {
        $this->classService->update($class, $request->validated());

        return to_route('admin.classes.index')->with('status', 'Kelas berhasil diperbarui.');
    }

    private function formData(CourseClass $class): array
    {
        return [
            'class' => $class,
            'programs' => Program::query()->orderBy('name')->get(),
            'instructors' => User::query()->instructors()->orderBy('full_name')->get(),
        ];
    }
}
