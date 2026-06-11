<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('program_promotions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('program_id')->constrained()->cascadeOnDelete();
            $table->string('title', 180);
            $table->text('description')->nullable();
            $table->enum('discount_type', ['percentage', 'fixed']);
            $table->decimal('discount_value', 12, 2);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('badge_label', 80)->nullable();
            $table->text('terms')->nullable();
            $table->timestamps();

            $table->index(['program_id', 'is_active'], 'idx_program_promotions_program_active');
            $table->index(['starts_at', 'ends_at'], 'idx_program_promotions_period');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('program_promotions');
    }
};
