<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RagKnowledgeChunk extends Model
{
    use HasFactory;

    protected $fillable = [
        'knowledge_source_id',
        'qdrant_point_id',
        'chunk_index',
        'content',
        'metadata',
        'embedding_model',
    ];

    protected function casts(): array
    {
        return [
            'chunk_index' => 'integer',
            'metadata' => 'array',
        ];
    }

    public function source(): BelongsTo
    {
        return $this->belongsTo(RagKnowledgeSource::class, 'knowledge_source_id');
    }
}
