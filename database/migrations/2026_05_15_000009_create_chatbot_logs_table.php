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
        Schema::create('chatbot_logs', function (Blueprint $table) {
            $table->id();
            $table->string('session_id', 64);
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('user_message');
            $table->text('bot_response');
            $table->string('intent', 50)->nullable();
            $table->boolean('is_helpful')->nullable();
            $table->timestamp('created_at')->nullable();

            $table->index('session_id', 'idx_chat_session');
            $table->index('intent', 'idx_chat_intent');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chatbot_logs');
    }
};
