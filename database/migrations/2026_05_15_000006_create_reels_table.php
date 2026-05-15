<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reels', function (Blueprint $table) {
            $table->id();
            $table->string('title', 200);
            $table->text('description')->nullable();
            $table->string('video_path', 500);
            $table->string('thumbnail_path')->nullable();
            $table->integer('duration_seconds')->nullable();
            $table->enum('category', ['promosi', 'dokumentasi', 'edukasi', 'testimoni', 'event'])->nullable()->default('edukasi');
            $table->unsignedInteger('views_count')->nullable()->default(0);
            $table->unsignedInteger('likes_count')->nullable()->default(0);
            $table->boolean('is_published')->nullable()->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index(['is_published', 'published_at'], 'idx_reels_published');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reels');
    }
};
