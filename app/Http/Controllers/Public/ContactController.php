<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Public\StoreContactMessageRequest;
use App\Models\Program;
use App\Services\ContactMessageService;
use App\Services\PublicDiscoveryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function index(Request $request, PublicDiscoveryService $discovery): View
    {
        return view('pages.public.contact.index', [
            'settings' => $discovery->settings(),
            'selectedProgram' => Program::query()
                ->where('is_active', true)
                ->find($request->integer('program')),
        ]);
    }

    public function store(StoreContactMessageRequest $request, ContactMessageService $messages): RedirectResponse
    {
        $messages->create($request->validated());

        return redirect()
            ->route('public.contact.index')
            ->with('status', 'Pesan kamu sudah terkirim. Tim ETC Planet akan menghubungi secepatnya.');
    }
}
