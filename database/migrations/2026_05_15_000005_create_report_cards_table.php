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
        Schema::create('report_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_id')->unique()->constrained('enrollments')->cascadeOnDelete();
            $table->tinyInteger('score_listening')->unsigned()->nullable();
            $table->tinyInteger('score_vocabulary')->unsigned()->nullable();
            $table->tinyInteger('score_structure')->unsigned()->nullable();
            $table->tinyInteger('score_reading')->unsigned()->nullable();
            $table->tinyInteger('score_writing')->unsigned()->nullable();
            $table->enum('grade_pronunciation', ['A', 'B', 'C', 'D'])->nullable();
            $table->enum('grade_sentence_arrangement', ['A', 'B', 'C', 'D'])->nullable();
            $table->enum('grade_class_participation', ['A', 'B', 'C', 'D'])->nullable();
            $table->enum('grade_questioning_skill', ['A', 'B', 'C', 'D'])->nullable();
            $table->enum('grade_analyzing_skill', ['A', 'B', 'C', 'D'])->nullable();
            $table->tinyInteger('total_score')->unsigned()->nullable();
            $table->enum('final_grade', ['A', 'B', 'C', 'D'])->nullable();
            $table->string('next_class', 100)->nullable();
            $table->text('comments')->nullable();
            $table->foreignId('instructor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('academic_director_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('managing_director_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('issued_at')->nullable();
            $table->string('pdf_path')->nullable();
            $table->boolean('is_published')->nullable()->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_cards');
    }
};
