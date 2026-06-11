<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Public\StoreChatbotMessageRequest;
use App\Services\ChatbotLogService;
use App\Services\PublicDiscoveryService;
use App\Services\RagChatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class ChatbotController extends Controller
{
    public function store(
        StoreChatbotMessageRequest $request,
        PublicDiscoveryService $discovery,
        ChatbotLogService $logs,
        RagChatService $rag,
    ): JsonResponse {
        $data = $request->validated();
        $sessionId = $data['session_id'] ?? $request->session()->get('public_chatbot_session_id') ?? (string) Str::uuid();
        $request->session()->put('public_chatbot_session_id', $sessionId);

        $answer = $rag->answer($data['message']);

        if ($answer['intent'] === 'rag_fallback') {
            $answer = $discovery->answerChatbot($data['message']);
        }

        $logs->logInteraction($sessionId, $data['message'], $answer['reply'], $answer['intent'], $request->user());

        return response()->json([
            'status' => 'ok',
            'session_id' => $sessionId,
            'intent' => $answer['intent'],
            'reply' => $answer['reply'],
        ]);
    }
}
