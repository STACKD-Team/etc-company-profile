<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class QdrantVectorService
{
    public function upsert(string $pointId, array $vector, array $payload): void
    {
        if (! $this->isConfigured()) {
            return;
        }

        Http::withHeaders($this->headers())
            ->timeout(30)
            ->put($this->baseUrl().'/collections/'.config('rag.qdrant.collection').'/points?wait=true', [
                'points' => [[
                    'id' => $pointId,
                    'vector' => $vector,
                    'payload' => $payload,
                ]],
            ])
            ->throw();
    }

    /**
     * @return array<int, array{content: string, score: float, metadata: array}>
     */
    public function search(array $vector, int $limit = 5): array
    {
        if (! $this->isConfigured()) {
            return [];
        }

        $response = Http::withHeaders($this->headers())
            ->timeout(30)
            ->post($this->baseUrl().'/collections/'.config('rag.qdrant.collection').'/points/search', [
                'vector' => $vector,
                'limit' => $limit,
                'with_payload' => true,
            ])
            ->throw()
            ->json('result', []);

        return collect($response)->map(fn (array $item): array => [
            'content' => (string) data_get($item, 'payload.content', ''),
            'score' => (float) ($item['score'] ?? 0),
            'metadata' => (array) data_get($item, 'payload.metadata', []),
        ])->filter(fn (array $item): bool => $item['content'] !== '')->values()->all();
    }

    public function isConfigured(): bool
    {
        return (bool) config('rag.qdrant.url');
    }

    protected function baseUrl(): string
    {
        return rtrim((string) config('rag.qdrant.url'), '/');
    }

    protected function headers(): array
    {
        return array_filter([
            'api-key' => config('rag.qdrant.api_key'),
        ]);
    }
}
