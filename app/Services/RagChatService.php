<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class RagChatService
{
    public function __construct(
        protected EmbeddingService $embeddings,
        protected QdrantVectorService $qdrant,
    ) {}

    /**
     * @return array{intent: string, reply: string, contexts: array}
     */
    public function answer(string $message): array
    {
        if (! $this->qdrant->isConfigured()) {
            return [
                'intent' => 'rag_fallback',
                'reply' => 'Aku belum menemukan knowledge base yang cukup untuk menjawab itu dengan yakin. Silakan hubungi admin ETC Planet atau coba tanyakan tentang program, jadwal, biaya, dan pendaftaran.',
                'contexts' => [],
            ];
        }

        try {
            $contexts = collect($this->qdrant->search(
                $this->embeddings->embed($message),
                (int) config('rag.top_k', 5),
            ))
                ->filter(fn (array $context): bool => ($context['score'] ?? 0) >= (float) config('rag.min_score', 0.60))
                ->values()
                ->all();
            $contextText = collect($contexts)->pluck('content')->filter()->implode("\n\n");

            if ($contextText === '') {
                return $this->fallback();
            }

            if (! $this->hasChatCompletion()) {
                return [
                    'intent' => 'rag',
                    'reply' => $this->contextOnlyReply($contextText),
                    'contexts' => $contexts,
                ];
            }

            $response = Http::withToken((string) config('rag.nvidia.api_key'))
                ->timeout(45)
                ->post(rtrim((string) config('rag.nvidia.base_url'), '/').'/chat/completions', [
                    'model' => config('rag.nvidia.model'),
                    'messages' => [
                        ['role' => 'system', 'content' => 'Jawab singkat dalam Bahasa Indonesia berdasarkan konteks ETC Planet. Jika konteks tidak cukup, katakan tidak tahu.'],
                        ['role' => 'user', 'content' => "Konteks:\n{$contextText}\n\nPertanyaan:\n{$message}"],
                    ],
                ])
                ->throw()
                ->json();
            $reply = data_get($response, 'choices.0.message.content');

            if (! is_string($reply) || blank($reply)) {
                return $this->fallback();
            }

            return [
                'intent' => 'rag',
                'reply' => trim($reply),
                'contexts' => $contexts,
            ];
        } catch (Throwable $exception) {
            Log::warning('Public RAG chatbot request failed.', [
                'exception' => $exception::class,
            ]);

            return $this->fallback();
        }
    }

    public function isConfigured(): bool
    {
        return $this->qdrant->isConfigured()
            && filled(config('rag.nvidia.base_url'))
            && filled(config('rag.nvidia.model'));
    }

    protected function hasChatCompletion(): bool
    {
        return filled(config('rag.nvidia.api_key'))
            && filled(config('rag.nvidia.base_url'))
            && filled(config('rag.nvidia.model'));
    }

    protected function contextOnlyReply(string $contextText): string
    {
        $sentence = trim((string) preg_split('/(?<=[.!?])\s+/', $contextText, 2)[0]);

        return $sentence !== '' ? $sentence : 'Aku menemukan konteks ETC Planet, tetapi belum bisa menyusun jawaban lengkap dari model saat ini.';
    }

    /**
     * @return array{intent: string, reply: string, contexts: array}
     */
    protected function fallback(): array
    {
        return [
            'intent' => 'rag_fallback',
            'reply' => 'Aku belum menemukan knowledge base yang cukup untuk menjawab itu dengan yakin. Silakan hubungi admin ETC Planet atau coba tanyakan tentang program, jadwal, biaya, dan pendaftaran.',
            'contexts' => [],
        ];
    }
}
