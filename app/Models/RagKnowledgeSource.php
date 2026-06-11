<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RagKnowledgeSource extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'source_type',
        'file_path',
        'file_name',
        'mime_type',
        'file_size',
        'status',
        'is_active',
        'uploaded_by',
        'extracted_text',
        'processed_at',
        'error_message',
    ];

    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
            'is_active' => 'boolean',
            'processed_at' => 'datetime',
        ];
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function chunks(): HasMany
    {
        return $this->hasMany(RagKnowledgeChunk::class, 'knowledge_source_id');
    }
}
