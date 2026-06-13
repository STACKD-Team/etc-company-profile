<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatbotLog;
use App\Services\ChatbotLogService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChatbotLogController extends Controller
{
    private const INTENTS = ['program', 'pricing', 'schedule', 'registration', 'contact', 'general'];

    public function __construct(private ChatbotLogService $logs) {}

    public function index(Request $request): View
    {
        return view('pages.admin.chatbot-log.index', [
            'logs' => $this->logs->adminPaginate($this->filters($request), 15),
            'intents' => self::INTENTS,
        ]);
    }

    public function show(ChatbotLog $chatbotLog): View
    {
        $chatbotLog->load('user');

        return view('pages.admin.chatbot-log.show', [
            'log' => $chatbotLog,
        ]);
    }

    private function filters(Request $request): array
    {
        $filters = $request->only(['session_id', 'intent', 'created_from', 'created_to', 'sort', 'direction']);

        if ($request->filled('is_helpful')) {
            $filters['is_helpful'] = $request->boolean('is_helpful');
        }

        return $filters;
    }
}
