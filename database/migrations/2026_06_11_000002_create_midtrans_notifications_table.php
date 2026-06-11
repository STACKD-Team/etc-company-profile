<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('midtrans_notifications', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('registration_id')->nullable()->constrained()->nullOnDelete();
            $table->string('order_id')->index();
            $table->string('transaction_id')->nullable()->index();
            $table->string('payment_type')->nullable();
            $table->string('transaction_status', 40)->index();
            $table->string('fraud_status', 40)->nullable();
            $table->string('status_code', 20)->nullable();
            $table->decimal('gross_amount', 12, 2)->nullable();
            $table->string('signature_key');
            $table->json('raw_payload');
            $table->enum('processing_status', ['received', 'processed', 'ignored', 'failed'])->default('received')->index();
            $table->timestamp('received_at')->useCurrent();
            $table->timestamp('processed_at')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->unique(['order_id', 'transaction_status', 'transaction_id'], 'midtrans_notifications_unique_event');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('midtrans_notifications');
    }
};
