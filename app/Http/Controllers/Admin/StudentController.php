<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DestroyAdminResourceRequest;
use App\Http\Requests\Admin\SaveAdminUserRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function __construct(private UserService $users) {}

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

        return view('pages.admin.student.index', compact('students'));
    }

    public function create(): View
    {
        return view('pages.admin.student.create', [
            'student' => new User(['role' => 'student', 'is_active' => true]),
        ]);
    }

    public function store(SaveAdminUserRequest $request): RedirectResponse
    {
        $student = $this->users->create($this->payload($request, 'student'));

        return to_route('admin.student.show', $student)->with('status', 'Siswa berhasil dibuat.');
    }

    public function show(User $student): View
    {
        abort_unless($student->role === 'student', 404);

        return view('pages.admin.student.show', [
            'student' => $student->load(['enrollments.courseClass.program', 'enrollments.courseClass.instructor', 'enrollments.reportCard', 'registrations.program']),
        ]);
    }

    public function edit(User $student): View
    {
        abort_unless($student->role === 'student', 404);

        return view('pages.admin.student.edit', compact('student'));
    }

    public function update(SaveAdminUserRequest $request, User $student): RedirectResponse
    {
        abort_unless($student->role === 'student', 404);

        $this->users->update($student, $this->payload($request, 'student', false));

        return to_route('admin.student.show', $student)->with('status', 'Siswa berhasil diperbarui.');
    }

    public function destroy(DestroyAdminResourceRequest $request, User $student): RedirectResponse
    {
        abort_unless($student->role === 'student', 404);

        $request->validated();
        $this->users->delete($student);

        return to_route('admin.student.index')->with('status', 'Siswa berhasil dihapus.');
    }

    private function payload(SaveAdminUserRequest $request, string $role, bool $creating = true): array
    {
        $data = $request->validated();

        if (! filled($data['password'] ?? null)) {
            unset($data['password']);
        }

        $data['role'] = $role;
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
