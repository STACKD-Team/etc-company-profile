<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class EmbeddingService
{
    public function isConfigured(): bool
    {
        return filled(config('rag.nvidia.api_key'))
            && filled(config('rag.nvidia.base_url'))
            && filled(config('rag.nvidia.embedding_model'));
    }

    /**
     * @return array<int, float>
     */
    public function embed(string $text): array
    {
        if (! $this->isConfigured()) {
            throw new \RuntimeException('NVIDIA embedding service is not configured.');
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

        $embedding = data_get($response, 'data.0.embedding');

        if (! is_array($embedding) || $embedding === []) {
            throw new \RuntimeException('NVIDIA embedding response is invalid.');
        }

        return array_map('floatval', $embedding);
    }
}
