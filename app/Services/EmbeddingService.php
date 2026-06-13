<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class EmbeddingService
{
    /**
     * @return array<int, float>
     */
    public function embed(string $text): array
    {
        if (! config('rag.nvidia.api_key')) {
            return $this->deterministicEmbedding($text);
        }

        $response = Http::withToken((string) config('rag.nvidia.api_key'))
            ->timeout(30)
            ->post(rtrim((string) config('rag.nvidia.base_url'), '/').'/embeddings', [
                'model' => config('rag.nvidia.embedding_model'),
                'input' => $text,
                'input_type' => 'passage',
            ])
            ->throw()
            ->json();

        return array_map('floatval', $response['data'][0]['embedding'] ?? $this->deterministicEmbedding($text));
    }

    /**
     * @return array<int, float>
     */
    protected function deterministicEmbedding(string $text): array
    {
        $hash = hash('sha256', $text);
        $values = [];

        for ($i = 0; $i < 32; $i += 2) {
            $values[] = (hexdec(substr($hash, $i, 2)) / 255) * 2 - 1;
        }

        return $values;
    }
}
