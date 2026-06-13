<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DestroyAdminResourceRequest;
use App\Http\Requests\Admin\StoreEnrollmentRequest;
use App\Models\CourseClass;
use App\Models\Enrollment;
use App\Models\User;
use App\Services\EnrollmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EnrollmentController extends Controller
{
    public function __construct(private EnrollmentService $enrollmentService) {}

    public function index(Request $request): View
    {
        return view('pages.admin.enrollment.index', [
            'enrollments' => $this->enrollmentService->paginate($request->only(['search', 'user_id', 'class_id', 'status', 'sort', 'direction']), 12),
            'students' => User::query()->students()->orderBy('full_name')->get(),
            'classes' => CourseClass::query()->with('program')->orderBy('name')->get(),
        ]);
    }

    public function store(StoreEnrollmentRequest $request): RedirectResponse
    {
        $enrollment = $this->enrollmentService->create($request->validated());

        return to_route('admin.enrollment.show', $enrollment)->with('status', 'Siswa berhasil dimasukkan ke kelas.');
    }

    public function edit(Enrollment $enrollment): View
    {
        return view('pages.admin.enrollment.edit', [
            'enrollment' => $enrollment,
            'students' => User::query()->students()->orderBy('full_name')->get(),
            'classes' => CourseClass::query()->with('program')->orderBy('name')->get(),
        ]);
    }

    public function update(StoreEnrollmentRequest $request, Enrollment $enrollment): RedirectResponse
    {
        $this->enrollmentService->update($enrollment, $request->validated());

        return to_route('admin.enrollment.show', $enrollment)->with('status', 'Enrollment berhasil diperbarui.');
    }

    public function show(Enrollment $enrollment): View
    {
        $enrollment->load([
            'user',
            'courseClass.program',
            'courseClass.instructor',
            'courseClass.room',
            'reportCard',
        ]);

        return view('pages.admin.enrollment.show', compact('enrollment'));
    }

    public function destroy(DestroyAdminResourceRequest $request, Enrollment $enrollment): RedirectResponse
    {
        $request->validated();
        $this->enrollmentService->delete($enrollment);

        return to_route('admin.enrollment.index')->with('status', 'Enrollment berhasil dihapus.');
    }
}
