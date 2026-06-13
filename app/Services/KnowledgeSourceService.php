<?php

namespace App\Services;

use App\Jobs\KnowledgeIndexingJob;
use App\Models\RagKnowledgeChunk;
use App\Models\RagKnowledgeSource;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Throwable;

class KnowledgeSourceService
{
    public function __construct(
        protected MediaStorageService $mediaStorage,
        protected TextExtractionService $extractor,
        protected EmbeddingService $embeddings,
        protected QdrantVectorService $qdrant,
    ) {}

    public function createFromUpload(array $data, UploadedFile $file, ?int $userId = null, bool $dispatch = true): RagKnowledgeSource
    {
        $source = RagKnowledgeSource::query()->create([
            'title' => $data['title'],
            'source_type' => 'upload',
            'file_path' => $this->mediaStorage->putUploadedFile($file, 'rag/knowledge-sources'),
            'file_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'status' => 'processing',
            'is_active' => (bool) ($data['is_active'] ?? false),
            'uploaded_by' => $userId,
        ]);

        try {
            $source->update([
                'extracted_text' => $this->extractor->extract($file),
                'error_message' => null,
            ]);
        } catch (Throwable $exception) {
            $source->update([
                'status' => 'failed',
                'processed_at' => now(),
                'error_message' => $exception->getMessage(),
            ]);

            return $source->refresh();
        }

        if ($dispatch) {
            KnowledgeIndexingJob::dispatch($source);
        }

        return $source;
    }

    public function reindex(RagKnowledgeSource $source): RagKnowledgeSource
    {
        $source->update([
            'status' => 'processing',
            'error_message' => null,
        ]);

        KnowledgeIndexingJob::dispatch($source);

        return $source->refresh();
    }

    public function publish(RagKnowledgeSource $source): RagKnowledgeSource
    {
        $source->update(['is_active' => true]);

        return $source->refresh();
    }

    public function unpublish(RagKnowledgeSource $source): RagKnowledgeSource
    {
        $source->update(['is_active' => false]);

        return $source->refresh();
    }

    public function archive(RagKnowledgeSource $source): RagKnowledgeSource
    {
        $source->update([
            'status' => 'archived',
            'is_active' => false,
        ]);

        return $source->refresh();
    }

    public function restore(RagKnowledgeSource $source): RagKnowledgeSource
    {
        $source->update([
            'status' => $source->chunks()->exists() ? 'ready' : 'draft',
        ]);

        return $source->refresh();
    }

    public function indexNow(RagKnowledgeSource $source): RagKnowledgeSource
    {
        try {
            $text = trim((string) $source->extracted_text);

            if ($text === '') {
                throw new \RuntimeException('Tidak ada teks yang bisa diindeks dari knowledge source ini.');
            }

            $source->chunks()->delete();

            foreach ($this->chunks($text) as $index => $chunk) {
                $pointId = (string) Str::uuid();
                $metadata = [
                    'source_id' => $source->id,
                    'title' => $source->title,
                    'chunk_index' => $index,
                ];
                $vector = $this->embeddings->embed($chunk);

                RagKnowledgeChunk::query()->create([
                    'knowledge_source_id' => $source->id,
                    'qdrant_point_id' => $pointId,
                    'chunk_index' => $index,
                    'content' => $chunk,
                    'metadata' => $metadata,
                    'embedding_model' => config('rag.nvidia.embedding_model'),
                ]);

                $this->qdrant->upsert($pointId, $vector, [
                    'content' => $chunk,
                    'metadata' => $metadata,
                ]);
            }

            $source->update([
                'status' => 'ready',
                'is_active' => true,
                'processed_at' => now(),
                'error_message' => null,
            ]);
        } catch (Throwable $exception) {
            $source->update([
                'status' => 'failed',
                'processed_at' => now(),
                'error_message' => $exception->getMessage(),
            ]);
        }

        return $source->refresh();
    }

    /**
     * @return array<int, string>
     */
    public function chunks(string $text): array
    {
        $size = max(200, (int) config('rag.chunk_size', 1000));
        $overlap = min((int) config('rag.chunk_overlap', 150), $size - 1);
        $chunks = [];

        for ($offset = 0; $offset < strlen($text); $offset += $size - $overlap) {
            $chunk = trim(substr($text, $offset, $size));

            if ($chunk !== '') {
                $chunks[] = $chunk;
            }
        }

        return $chunks;
    }
}
