<?php

namespace App\Services;

use App\Models\RagKnowledgeChunk;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class RagChatService
{
    public function __construct(
        protected EmbeddingService $embeddings,
        protected QdrantVectorService $qdrant,
        protected ?PublicChatbotDataService $publicData = null,
    ) {}

    /**
     * @return array{intent: string, reply: string, contexts: array, links: array}
     */
    public function answer(string $message): array
    {
        try {
            $analysis = $this->analyzeMessage($message);

            if (($analysis['scope'] ?? 'etc') === 'out_of_scope') {
                return [
                    'intent' => 'rag_out_of_scope',
                    'reply' => 'Aku hanya bisa membantu menjawab informasi seputar ETC Planet Padang.',
                    'contexts' => [],
                    'links' => [],
                ];
            }

            $query = trim((string) ($analysis['retrieval_query'] ?? $message));
            $query = $query !== '' ? $query : $message;
            $publicContext = $this->publicData()->contextFor($query, (array) ($analysis['public_data_needed'] ?? []));
            $contexts = $this->retrievedContexts($query);
            $contextText = collect($contexts)->pluck('content')->filter()->implode("\n\n");
            $publicText = $this->publicContextText($publicContext['items']);

            if ($contextText === '' && $publicText === '') {
                return $this->noContext();
            }

            if (! $this->hasChatCompletion()) {
                return [
                    'intent' => 'rag',
                    'reply' => $contextText !== '' ? $this->contextOnlyReply($contextText) : $this->publicOnlyReply($publicContext['items']),
                    'contexts' => $contexts,
                    'links' => $publicContext['links'],
                ];
            }

            $reply = $this->generateReply($message, $contextText, $publicText);

            if (! is_string($reply) || blank($reply)) {
                return $this->noContext();
            }

            if (Str::contains(Str::lower($reply), 'aku tidak tahu berdasarkan knowledge base')) {
                return $this->noContext();
            }

            return [
                'intent' => 'rag',
                'reply' => trim($reply),
                'contexts' => $contexts,
                'links' => $publicContext['links'],
            ];
        } catch (Throwable $exception) {
            Log::warning('Public RAG chatbot request failed.', $this->exceptionContext($exception));

            $fallback = $this->localPublicFallback($message);

            if ($fallback !== null) {
                return $fallback;
            }

            return [
                'intent' => 'rag_error',
                'reply' => 'Aku belum bisa memeriksa knowledge base ETC Planet saat ini. Silakan coba lagi sebentar lagi.',
                'contexts' => [],
                'links' => [],
            ];
        }
    }

    public function isConfigured(): bool
    {
        return $this->hasChatCompletion() || $this->qdrant->isConfigured();
    }

    protected function hasChatCompletion(): bool
    {
        return filled(config('rag.nvidia.api_key'))
            && filled(config('rag.nvidia.base_url'))
            && filled(config('rag.nvidia.model'));
    }

    /**
     * @return array{scope: string, retrieval_query: string, public_data_needed: array<int, string>}
     */
    protected function analyzeMessage(string $message): array
    {
        if (! $this->hasChatCompletion()) {
            return $this->localAnalysis($message);
        }

        $response = Http::withToken((string) config('rag.nvidia.api_key'))
            ->timeout(45)
            ->post(rtrim((string) config('rag.nvidia.base_url'), '/').'/chat/completions', [
                'model' => config('rag.nvidia.model'),
                'messages' => [
                    ['role' => 'system', 'content' => implode("\n", [
                        'Kamu adalah router retrieval untuk chatbot ETC Planet Padang.',
                        'Balas hanya JSON valid tanpa markdown.',
                        'Schema: {"scope":"etc|out_of_scope","retrieval_query":"string","public_data_needed":["programs","classes","rooms","partners","gallery","testimonials","faq","profile","contact","instructors","reels"]}.',
                        'Scope etc hanya untuk pertanyaan tentang ETC Planet Padang, program, kelas, biaya, jadwal, pendaftaran, placement test, fasilitas, kontak, pengajar, konten publik, atau bantuan penggunaan website ETC.',
                        'Jika pertanyaan meminta topik umum di luar ETC Planet Padang, set scope out_of_scope.',
                    ])],
                    ['role' => 'user', 'content' => $message],
                ],
                'temperature' => 0,
            ])
            ->throw()
            ->json();

        $content = data_get($response, 'choices.0.message.content');
        $decoded = is_string($content) ? json_decode(trim($content), true) : null;

        if (! is_array($decoded)) {
            return $this->localAnalysis($message);
        }

        return [
            'scope' => ($decoded['scope'] ?? 'etc') === 'out_of_scope' ? 'out_of_scope' : 'etc',
            'retrieval_query' => trim((string) ($decoded['retrieval_query'] ?? $message)) ?: $message,
            'public_data_needed' => array_values(array_filter((array) ($decoded['public_data_needed'] ?? []), 'is_string')),
        ];
    }

    /**
     * @return array{scope: string, retrieval_query: string, public_data_needed: array<int, string>}
     */
    protected function localAnalysis(string $message): array
    {
        $text = str($message)->lower()->toString();
        $etcKeywords = [
            'etc',
            'planet',
            'padang',
            'program',
            'kursus',
            'kelas',
            'bahasa',
            'inggris',
            'english',
            'toefl',
            'ielts',
            'toeic',
            'mandarin',
            'japanese',
            'biaya',
            'harga',
            'pembayaran',
            'jadwal',
            'daftar',
            'pendaftaran',
            'placement',
            'test',
            'ruang',
            'fasilitas',
            'kontak',
            'alamat',
            'instruktur',
            'pengajar',
            'reels',
            'video',
        ];
        $outsideKeywords = ['presiden', 'politik', 'coding', 'programming', 'resep', 'dokter', 'penyakit', 'hukum'];

        return [
            'scope' => Str::contains($text, $outsideKeywords) && ! Str::contains($text, ['etc', 'planet'])
                ? 'out_of_scope'
                : (Str::contains($text, $etcKeywords) ? 'etc' : 'out_of_scope'),
            'retrieval_query' => $message,
            'public_data_needed' => [],
        ];
    }

    /**
     * @return array<int, array{content: string, score: float, metadata: array}>
     */
    protected function retrievedContexts(string $query): array
    {
        if (! $this->qdrant->isConfigured()) {
            return $this->localKnowledgeContexts($query);
        }

        try {
            $contexts = collect($this->qdrant->search(
                $this->embeddings->embed($query, 'query'),
                (int) config('rag.top_k', 5),
            ))
                ->filter(fn (array $context): bool => ($context['score'] ?? 0) >= (float) config('rag.min_score', 0.60))
                ->values()
                ->all();

            return $contexts !== [] ? $contexts : $this->localKnowledgeContexts($query);
        } catch (Throwable $exception) {
            Log::warning('Public RAG vector retrieval failed.', $this->exceptionContext($exception));

            return $this->localKnowledgeContexts($query);
        }
    }

    protected function contextOnlyReply(string $contextText): string
    {
        $sentence = trim((string) preg_split('/(?<=[.!?])\s+/', $contextText, 2)[0]);

        return $sentence !== '' ? $sentence : 'Aku menemukan konteks ETC Planet, tetapi belum bisa menyusun jawaban lengkap dari model saat ini.';
    }

    /**
     * @param array<int, array<string, mixed>> $items
     */
    protected function publicContextText(array $items): string
    {
        return collect($items)
            ->map(fn (array $item): string => json_encode($item, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE))
            ->filter()
            ->implode("\n");
    }

    /**
     * @param array<int, array<string, mixed>> $items
     */
    protected function publicOnlyReply(array $items): string
    {
        $settings = collect($items)
            ->filter(fn (array $item): bool => ($item['type'] ?? null) === 'profile')
            ->pluck('meta')
            ->filter(fn (mixed $meta): bool => is_array($meta))
            ->reduce(fn (array $carry, array $meta): array => array_merge($carry, $meta), []);

        $address = $settings['address'] ?? null;
        $contact = $settings['whatsapp'] ?? $settings['phone'] ?? null;
        $instagram = $settings['instagram'] ?? null;
        $contactParts = array_filter([
            $address ? 'ETC Planet berlokasi di '.$address.'.' : null,
            $contact ? 'Kamu dapat menghubungi '.$contact.' untuk konsultasi.' : null,
            $instagram ? 'Instagram ETC Planet: '.$instagram.'.' : null,
        ]);

        if ($contactParts !== []) {
            return implode(' ', $contactParts);
        }

        $faq = collect($items)->first(fn (array $item): bool => ($item['type'] ?? null) === 'faq' && filled($item['body'] ?? null));

        if ($faq !== null) {
            return (string) $faq['body'];
        }

        $titles = collect($items)->pluck('title')->filter()->take(3)->implode(', ');

        return $titles !== ''
            ? 'Aku menemukan data publik ETC Planet yang relevan: '.$titles.'. Silakan buka link terkait untuk detailnya.'
            : 'Aku tidak tahu berdasarkan knowledge base ETC Planet saat ini.';
    }

    /**
     * @return array<int, array{content: string, score: float, metadata: array}>
     */
    protected function localKnowledgeContexts(string $query): array
    {
        $terms = $this->searchTerms($query);

        if ($terms === []) {
            return [];
        }

        $chunks = RagKnowledgeChunk::query()
            ->whereHas('source', fn ($query) => $query
                ->where('status', 'ready')
                ->where('is_active', true))
            ->where(function ($query) use ($terms): void {
                foreach ($terms as $term) {
                    $query->orWhere('content', 'like', '%'.$term.'%');
                }
            })
            ->limit(8)
            ->get();

        return $chunks
            ->map(function (RagKnowledgeChunk $chunk) use ($terms): array {
                $content = (string) $chunk->content;
                $score = collect($terms)
                    ->filter(fn (string $term): bool => Str::contains(Str::lower($content), Str::lower($term)))
                    ->count() / max(1, count($terms));

                return [
                    'content' => $content,
                    'score' => round($score, 4),
                    'metadata' => array_merge((array) $chunk->metadata, [
                        'source' => 'local_rag_chunk',
                        'knowledge_source_id' => $chunk->knowledge_source_id,
                        'chunk_index' => $chunk->chunk_index,
                    ]),
                ];
            })
            ->sortByDesc('score')
            ->take((int) config('rag.top_k', 5))
            ->values()
            ->all();
    }

    /**
     * @return array<int, string>
     */
    protected function searchTerms(string $query): array
    {
        $terms = Str::of($query)
            ->lower()
            ->replaceMatches('/[^\pL\pN]+/u', ' ')
            ->explode(' ')
            ->map(fn (string $term): string => trim($term))
            ->filter(fn (string $term): bool => mb_strlen($term) >= 4)
            ->reject(fn (string $term): bool => in_array($term, [
                'siapa',
                'apakah',
                'yang',
                'dari',
                'etc',
                'padang',
                'planet',
                'adalah',
            ], true))
            ->values();

        if (Str::contains(Str::lower($query), ['pendiri', 'founder', 'pemilik'])) {
            $terms = $terms->merge(['debby', 'susiyanti', 'pemilik', 'managing', 'director']);
        }

        return $terms->unique()->values()->all();
    }

    protected function generateReply(string $message, string $contextText, string $publicText): ?string
    {
        $response = Http::withToken((string) config('rag.nvidia.api_key'))
            ->timeout(45)
            ->post(rtrim((string) config('rag.nvidia.base_url'), '/').'/chat/completions', [
                'model' => config('rag.nvidia.model'),
                'messages' => [
                    ['role' => 'system', 'content' => implode("\n", [
                        'Kamu adalah chatbot ETC Planet Padang.',
                        'Jawab singkat dalam Bahasa Indonesia.',
                        'Gunakan hanya Konteks RAG dan Data Publik yang diberikan.',
                        'Jangan menambahkan fakta di luar konteks.',
                        'Jangan menyebut sumber internal seperti dokumen, konteks, data publik, knowledge base, atau database kepada user.',
                        'Jangan menulis HTML, markdown link, atau URL mentah. Link akan dirender oleh sistem.',
                        'Jika konteks tidak cukup untuk menjawab pertanyaan, jawab: Aku tidak tahu berdasarkan knowledge base ETC Planet saat ini.',
                    ])],
                    ['role' => 'user', 'content' => "Konteks RAG:\n{$contextText}\n\nData Publik:\n{$publicText}\n\nPertanyaan:\n{$message}"],
                ],
                'temperature' => 0.2,
            ])
            ->throw()
            ->json();

        $reply = data_get($response, 'choices.0.message.content');

        return is_string($reply) ? trim(strip_tags($reply)) : null;
    }

    /**
     * @return array{intent: string, reply: string, contexts: array, links: array}
     */
    protected function noContext(): array
    {
        return [
            'intent' => 'rag_no_context',
            'reply' => 'Aku tidak tahu berdasarkan knowledge base ETC Planet saat ini.',
            'contexts' => [],
            'links' => [],
        ];
    }

    /**
     * @return array{intent: string, reply: string, contexts: array, links: array}|null
     */
    protected function localPublicFallback(string $message): ?array
    {
        $analysis = $this->localAnalysis($message);

        if (($analysis['scope'] ?? 'etc') === 'out_of_scope') {
            return null;
        }

        $publicContext = $this->publicData()->contextFor(
            (string) ($analysis['retrieval_query'] ?? $message),
            (array) ($analysis['public_data_needed'] ?? []),
        );
        $contexts = $this->localKnowledgeContexts((string) ($analysis['retrieval_query'] ?? $message));

        if ($contexts !== []) {
            return [
                'intent' => 'rag',
                'reply' => $this->localKnowledgeReply($message, $contexts),
                'contexts' => $contexts,
                'links' => $publicContext['links'],
            ];
        }

        if ($publicContext['items'] === []) {
            return null;
        }

        return [
            'intent' => 'rag',
            'reply' => $this->publicOnlyReply($publicContext['items']),
            'contexts' => [],
            'links' => $publicContext['links'],
        ];
    }

    /**
     * @param array<int, array{content: string, score: float, metadata: array}> $contexts
     */
    protected function localKnowledgeReply(string $message, array $contexts): string
    {
        $text = collect($contexts)->pluck('content')->implode("\n\n");

        if (Str::contains(Str::lower($message), ['pendiri', 'founder', 'pemilik'])) {
            if (preg_match('/Debby\s+Susiyanti(?:,\s*S\.Pd)?/i', $text, $match)) {
                return $match[0].' adalah pemilik Lembaga Pendidikan dan Pelatihan Kerja ETC sekaligus Managing Director.';
            }
        }

        return $this->contextOnlyReply($text);
    }

    /**
     * @return array<string, mixed>
     */
    protected function exceptionContext(Throwable $exception): array
    {
        $context = [
            'exception' => $exception::class,
            'message' => Str::limit($exception->getMessage(), 240),
        ];

        if (method_exists($exception, 'response') && $exception->response()) {
            $context['status'] = $exception->response()->status();
            $context['body'] = Str::limit($exception->response()->body(), 500);
        }

        return $context;
    }

    protected function publicData(): PublicChatbotDataService
    {
        return $this->publicData ??= app(PublicChatbotDataService::class);
    }
}
