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
        Schema::create('registrations', function (Blueprint $table) {
            $table->id();
            $table->string('registration_code', 30)->unique();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('program_id')->constrained('programs')->restrictOnDelete();
            $table->foreignId('class_id')->nullable()->constrained('classes')->nullOnDelete();
            $table->string('applicant_name', 150);
            $table->string('applicant_email', 150);
            $table->string('applicant_phone', 20);
            $table->enum('preferred_days', ['mon_wed', 'tue_thu', 'wed_fri', 'sat_sun', 'request'])->nullable();
            $table->string('preferred_time', 20)->nullable();
            $table->dateTime('placement_test_at')->nullable();
            $table->text('placement_test_result')->nullable();
            $table->enum('payment_method', ['qris', 'bank_transfer', 'virtual_account', 'ewallet'])->nullable();
            $table->decimal('payment_amount', 12, 2)->nullable();
            $table->string('payment_gateway_id', 100)->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->enum('status', ['pending_payment', 'paid', 'placement_test', 'enrolled', 'rejected', 'cancelled'])->default('pending_payment');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('status', 'idx_reg_status');
            $table->index('applicant_email', 'idx_reg_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registrations');
    }
};
