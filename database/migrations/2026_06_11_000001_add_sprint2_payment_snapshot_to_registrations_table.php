<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registrations', function (Blueprint $table): void {
            $table->string('midtrans_order_id', 120)->nullable()->after('payment_gateway_id')->index();
            $table->string('midtrans_snap_token', 255)->nullable()->after('midtrans_order_id');
            $table->string('midtrans_redirect_url', 500)->nullable()->after('midtrans_snap_token');
            $table->text('payment_status_message')->nullable()->after('payment_status');
            $table->decimal('original_amount', 12, 2)->nullable()->after('payment_amount');
            $table->decimal('discount_amount', 12, 2)->nullable()->after('original_amount');
            $table->decimal('final_amount', 12, 2)->nullable()->after('discount_amount');
            $table->foreignId('program_promotion_id')->nullable()->after('final_amount')->constrained('program_promotions')->nullOnDelete();
            $table->string('program_promotion_title', 180)->nullable()->after('program_promotion_id');
        });
    }

    public function down(): void
    {
        Schema::table('registrations', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('program_promotion_id');
            $table->dropColumn([
                'midtrans_order_id',
                'midtrans_snap_token',
                'midtrans_redirect_url',
                'payment_status_message',
                'original_amount',
                'discount_amount',
                'final_amount',
                'program_promotion_title',
            ]);
        });
    }
};
