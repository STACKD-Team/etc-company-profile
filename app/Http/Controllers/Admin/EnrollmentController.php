<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreEnrollmentRequest;
use App\Models\CourseClass;
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
        return view('admin.enrollments.index', [
            'enrollments' => $this->enrollmentService->paginate($request->only(['search', 'user_id', 'class_id', 'status', 'sort', 'direction']), 12),
            'students' => User::query()->students()->orderBy('full_name')->get(),
            'classes' => CourseClass::query()->with('program')->orderBy('name')->get(),
        ]);
    }

    public function store(StoreEnrollmentRequest $request): RedirectResponse
    {
        $this->enrollmentService->create($request->validated());

        return to_route('admin.enrollments.index')->with('status', 'Siswa berhasil dimasukkan ke kelas.');
    }
}
