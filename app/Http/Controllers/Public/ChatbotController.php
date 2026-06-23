<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Public\StoreChatbotMessageRequest;
use App\Services\ChatbotLogService;
use App\Services\RagChatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class ChatbotController extends Controller
{
    public function store(
        StoreChatbotMessageRequest $request,
        ChatbotLogService $logs,
        RagChatService $rag,
    ): JsonResponse {
        $data = $request->validated();
        $sessionId = $data['session_id'] ?? $request->session()->get('public_chatbot_session_id') ?? (string) Str::uuid();
        $request->session()->put('public_chatbot_session_id', $sessionId);

        try {
            $answer = $rag->answer($data['message']);
        } catch (Throwable $exception) {
            Log::warning('Public chatbot could not start the RAG flow.', [
                'exception' => $exception::class,
            ]);
            $answer = [
                'intent' => 'rag_error',
                'reply' => 'Aku belum bisa memeriksa knowledge base ETC Planet saat ini. Silakan coba lagi sebentar lagi.',
                'links' => [],
            ];
        }

        $logs->logInteraction($sessionId, $data['message'], $answer['reply'], $answer['intent'], $request->user());

        $payload = [
            'status' => 'ok',
            'session_id' => $sessionId,
            'intent' => $answer['intent'],
            'reply' => $answer['reply'],
        ];

        if (! empty($answer['links'])) {
            $payload['links'] = $answer['links'];
        }

        return response()->json($payload);
    }
}
