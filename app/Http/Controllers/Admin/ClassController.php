<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DestroyAdminResourceRequest;
use App\Http\Requests\Admin\StoreCourseClassRequest;
use App\Models\CourseClass;
use App\Models\Program;
use App\Models\Room;
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
        return view('pages.admin.class.index', [
            'classes' => $this->classService->paginate($request->only(['search', 'program_id', 'instructor_id', 'status', 'sort', 'direction']), 12),
            'programs' => Program::query()->orderBy('name')->get(),
            'instructors' => User::query()->instructors()->orderBy('full_name')->get(),
            'rooms' => Room::query()->where('is_active', true)->orderBy('display_order')->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('pages.admin.class.create', $this->formData(new CourseClass()));
    }

    public function store(StoreCourseClassRequest $request): RedirectResponse
    {
        $class = $this->classService->create($request->validated());

        return to_route('admin.class.show', $class)->with('status', 'Kelas berhasil dibuat.');
    }

    public function show(CourseClass $class): View
    {
        $class->load([
            'program',
            'instructor',
            'room',
            'enrollments.user',
            'enrollments.reportCard',
        ]);

        return view('pages.admin.class.show', compact('class'));
    }

    public function edit(CourseClass $class): View
    {
        return view('pages.admin.class.edit', $this->formData($class));
    }

    public function update(StoreCourseClassRequest $request, CourseClass $class): RedirectResponse
    {
        $this->classService->update($class, $request->validated());

        return to_route('admin.class.show', $class)->with('status', 'Kelas berhasil diperbarui.');
    }

    public function destroy(DestroyAdminResourceRequest $request, CourseClass $class): RedirectResponse
    {
        $request->validated();
        $this->classService->delete($class);

        return to_route('admin.class.index')->with('status', 'Kelas berhasil dihapus.');
    }

    private function formData(CourseClass $class): array
    {
        return [
            'class' => $class,
            'programs' => Program::query()->orderBy('name')->get(),
            'instructors' => User::query()->instructors()->orderBy('full_name')->get(),
            'rooms' => Room::query()->where('is_active', true)->orderBy('display_order')->orderBy('name')->get(),
        ];
    }
}
