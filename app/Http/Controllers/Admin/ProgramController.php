<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProgramRequest;
use App\Http\Requests\Admin\UpdateProgramRequest;
use App\Models\Program;
use App\Services\ProgramService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProgramController extends Controller
{
    public function __construct(private ProgramService $programService) {}

    public function index(Request $request): View
    {
        $filters = $request->only(['search', 'category', 'type', 'target_age', 'sort', 'direction']);

        if ($request->filled('is_active')) {
            $filters['is_active'] = $request->boolean('is_active');
        }

        return view('admin.programs.index', [
            'programs' => $this->programService->paginate($filters, 12),
        ]);
    }

    public function create(): View
    {
        return view('admin.programs.create', ['program' => new Program()]);
    }

    public function store(StoreProgramRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');

        $this->programService->create($data);

        return to_route('admin.programs.index')->with('status', 'Program berhasil dibuat.');
    }

    public function edit(Program $program): View
    {
        return view('admin.programs.edit', compact('program'));
    }

    public function update(UpdateProgramRequest $request, Program $program): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');

        $this->programService->update($program, $data);

        return to_route('admin.programs.index')->with('status', 'Program berhasil diperbarui.');
    }
}
