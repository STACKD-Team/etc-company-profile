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
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained('programs')->cascadeOnDelete();
            $table->foreignId('instructor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name', 100);
            $table->string('schedule_days', 50)->nullable();
            $table->string('schedule_time', 50)->nullable();
            $table->string('room', 50)->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->enum('status', ['upcoming', 'ongoing', 'completed', 'cancelled'])->nullable()->default('upcoming');
            $table->timestamps();
            $table->softDeletes();

            $table->index('status', 'idx_classes_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};
