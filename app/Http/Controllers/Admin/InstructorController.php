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
        $instructors = User::query()
            ->instructors()
            ->withCount('classesTaught')
            ->when($request->string('search')->toString(), fn ($query, string $search) => $query
                ->where(fn ($query) => $query
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('full_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")))
            ->orderBy('full_name')
            ->paginate(12)
            ->withQueryString();

        return view('admin.instructors.index', compact('instructors'));
    }

    public function show(User $instructor): View
    {
        abort_unless($instructor->role === 'instructor', 404);

        return view('admin.instructors.show', [
            'instructor' => $instructor->load(['classesTaught.program']),
        ]);
    }
}
