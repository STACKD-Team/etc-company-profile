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
        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('slug', 170)->unique();
            $table->enum('category', ['english', 'mandarin', 'japanese', 'test_prep', 'soft_skills', 'other'])->default('english');
            $table->enum('type', ['regular', 'private', 'one_on_one'])->default('regular');
            $table->enum('target_age', ['kids', 'teen', 'adult', 'university', 'all'])->nullable()->default('all');
            $table->text('description')->nullable();
            $table->integer('duration_meetings')->nullable()->default(16);
            $table->integer('max_students')->nullable()->default(10);
            $table->decimal('price', 12, 2)->default(0);
            $table->decimal('registration_fee', 12, 2)->default(200000);
            $table->string('thumbnail')->nullable();
            $table->boolean('is_active')->nullable()->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('category', 'idx_programs_category');
            $table->index('is_active', 'idx_programs_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('programs');
    }
};
