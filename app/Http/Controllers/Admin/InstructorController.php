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

class InstructorController extends Controller
{
    public function __construct(private UserService $users) {}

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

    public function create(): View
    {
        return view('pages.admin.instructor.create', [
            'instructor' => new User(['role' => 'instructor', 'is_active' => true, 'show_on_team_page' => true]),
        ]);
    }

    public function store(SaveAdminUserRequest $request): RedirectResponse
    {
        $instructor = $this->users->create($this->payload($request, 'instructor'));

        return to_route('admin.instructor.show', $instructor)->with('status', 'Instructor berhasil dibuat.');
    }

    public function show(User $instructor): View
    {
        abort_unless($instructor->role === 'instructor', 404);

        return view('pages.admin.instructor.show', [
            'instructor' => $instructor->load(['classesTaught.program']),
        ]);
    }

    public function edit(User $instructor): View
    {
        abort_unless($instructor->role === 'instructor', 404);

        return view('pages.admin.instructor.edit', compact('instructor'));
    }

    public function update(SaveAdminUserRequest $request, User $instructor): RedirectResponse
    {
        abort_unless($instructor->role === 'instructor', 404);

        $this->users->update($instructor, $this->payload($request, 'instructor'));

        return to_route('admin.instructor.show', $instructor)->with('status', 'Instructor berhasil diperbarui.');
    }

    public function destroy(DestroyAdminResourceRequest $request, User $instructor): RedirectResponse
    {
        abort_unless($instructor->role === 'instructor', 404);

        $request->validated();
        $this->users->delete($instructor);

        return to_route('admin.instructor.index')->with('status', 'Instructor berhasil dihapus.');
    }

    private function payload(SaveAdminUserRequest $request, string $role): array
    {
        $data = $request->validated();

        if (! filled($data['password'] ?? null)) {
            unset($data['password']);
        }

        $data['role'] = $role;
        $data['is_active'] = $request->boolean('is_active');
        $data['show_on_team_page'] = $request->boolean('show_on_team_page');

        return $data;
    }
}
