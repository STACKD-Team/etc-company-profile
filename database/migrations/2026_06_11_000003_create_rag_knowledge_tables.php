<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rag_knowledge_sources', function (Blueprint $table): void {
            $table->id();
            $table->string('title');
            $table->enum('source_type', ['upload', 'manual', 'url', 'faq'])->default('upload');
            $table->string('file_path', 500)->nullable();
            $table->string('file_name')->nullable();
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->enum('status', ['draft', 'processing', 'ready', 'failed', 'archived'])->default('draft')->index();
            $table->boolean('is_active')->default(false)->index();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->longText('extracted_text')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
        });

        Schema::create('rag_knowledge_chunks', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('knowledge_source_id')->constrained('rag_knowledge_sources')->cascadeOnDelete();
            $table->string('qdrant_point_id')->index();
            $table->unsignedInteger('chunk_index');
            $table->longText('content');
            $table->json('metadata')->nullable();
            $table->string('embedding_model')->nullable();
            $table->timestamps();

            $table->unique(['knowledge_source_id', 'chunk_index'], 'rag_chunks_source_index_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rag_knowledge_chunks');
        Schema::dropIfExists('rag_knowledge_sources');
    }
};
