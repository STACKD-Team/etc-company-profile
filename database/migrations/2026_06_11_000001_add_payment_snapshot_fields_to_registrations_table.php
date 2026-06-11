<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registrations', function (Blueprint $table): void {
            $table->string('payment_status', 40)->nullable()->after('payment_method');
            $table->decimal('payment_original_amount', 12, 2)->nullable()->after('payment_amount');
            $table->decimal('payment_discount_amount', 12, 2)->nullable()->after('payment_original_amount');
            $table->decimal('payment_final_amount', 12, 2)->nullable()->after('payment_discount_amount');
            $table->foreignId('payment_promotion_id')->nullable()->after('payment_final_amount')->constrained('program_promotions')->nullOnDelete();
            $table->string('payment_promotion_title', 180)->nullable()->after('payment_promotion_id');
            $table->string('payment_redirect_url')->nullable()->after('payment_gateway_id');
            $table->string('payment_snap_token')->nullable()->after('payment_redirect_url');
            $table->timestamp('payment_expires_at')->nullable()->after('payment_snap_token');

            $table->index('payment_status', 'idx_reg_payment_status');
            $table->index('payment_expires_at', 'idx_reg_payment_expires_at');
        });
    }

    public function down(): void
    {
        Schema::table('registrations', function (Blueprint $table): void {
            $table->dropIndex('idx_reg_payment_status');
            $table->dropIndex('idx_reg_payment_expires_at');
            $table->dropConstrainedForeignId('payment_promotion_id');
            $table->dropColumn([
                'payment_status',
                'payment_original_amount',
                'payment_discount_amount',
                'payment_final_amount',
                'payment_promotion_title',
                'payment_redirect_url',
                'payment_snap_token',
                'payment_expires_at',
            ]);
        });
    }
};
