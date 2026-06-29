<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Services\ContactMessageService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactMessageController extends Controller
{
    public function __construct(private ContactMessageService $messages) {}

    public function index(Request $request): View
    {
        return view('pages.admin.contact-message.index', [
            'messages' => $this->messages->adminPaginate($this->filters($request), 15),
        ]);
    }

    public function show(ContactMessage $contactMessage): View
    {
        if (! $contactMessage->is_read) {
            $contactMessage = $this->messages->markAsRead($contactMessage);
        }

        return view('pages.admin.contact-message.show', [
            'message' => $contactMessage,
        ]);
    }

    private function filters(Request $request): array
    {
        $filters = $request->only(['search', 'sort', 'direction']);

        if ($request->filled('is_read')) {
            $filters['is_read'] = $request->boolean('is_read');
        }

        return $filters;
    }
}
