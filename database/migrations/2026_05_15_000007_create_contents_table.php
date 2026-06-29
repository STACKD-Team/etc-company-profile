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
        Schema::create('contents', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['gallery', 'partner', 'profile', 'faq', 'testimonial']);
            $table->string('title', 200);
            $table->string('slug', 220)->nullable();
            $table->longText('body')->nullable();
            $table->string('image', 500)->nullable();
            $table->json('images')->nullable();
            $table->json('meta')->nullable();
            $table->integer('display_order')->nullable()->default(0);
            $table->boolean('is_published')->nullable()->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('type', 'idx_contents_type');
            $table->index('slug', 'idx_contents_slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contents');
    }
};
