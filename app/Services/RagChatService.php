<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

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
        $contexts = $this->qdrant->search($this->embeddings->embed($message), (int) config('rag.top_k', 5));
        $contextText = collect($contexts)->pluck('content')->implode("\n\n");

        if ($contextText === '') {
            return [
                'intent' => 'rag_fallback',
                'reply' => 'Aku belum menemukan knowledge base yang cukup untuk menjawab itu dengan yakin. Silakan hubungi admin ETC Planet atau coba tanyakan tentang program, jadwal, biaya, dan pendaftaran.',
                'contexts' => [],
            ];
        }

        if (! config('rag.nvidia.api_key')) {
            return [
                'intent' => 'rag',
                'reply' => 'Berdasarkan knowledge ETC: '.str($contextText)->limit(450)->toString(),
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

        return [
            'intent' => 'rag',
            'reply' => (string) data_get($response, 'choices.0.message.content', 'Aku belum bisa menjawab pertanyaan itu dengan yakin.'),
            'contexts' => $contexts,
        ];
    }
}
