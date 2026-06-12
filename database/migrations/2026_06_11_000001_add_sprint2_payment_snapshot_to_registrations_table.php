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
            if (! Schema::hasColumn('registrations', 'payment_status')) {
                $table->string('payment_status', 40)->nullable()->after('midtrans_redirect_url')->index();
            }
            $table->text('payment_status_message')->nullable()->after('payment_status');
            if (! Schema::hasColumn('registrations', 'payment_expires_at')) {
                $table->timestamp('payment_expires_at')->nullable()->after('payment_status_message');
            }
            if (! Schema::hasColumn('registrations', 'payment_redirect_url')) {
                $table->string('payment_redirect_url', 500)->nullable()->after('payment_expires_at');
            }
            if (! Schema::hasColumn('registrations', 'payment_snap_token')) {
                $table->string('payment_snap_token', 255)->nullable()->after('payment_redirect_url');
            }
            if (! Schema::hasColumn('registrations', 'payment_original_amount')) {
                $table->decimal('payment_original_amount', 12, 2)->nullable()->after('payment_snap_token');
            }
            if (! Schema::hasColumn('registrations', 'payment_discount_amount')) {
                $table->decimal('payment_discount_amount', 12, 2)->nullable()->after('payment_original_amount');
            }
            if (! Schema::hasColumn('registrations', 'payment_final_amount')) {
                $table->decimal('payment_final_amount', 12, 2)->nullable()->after('payment_discount_amount');
            }
            $table->decimal('original_amount', 12, 2)->nullable()->after('payment_amount');
            $table->decimal('discount_amount', 12, 2)->nullable()->after('original_amount');
            $table->decimal('final_amount', 12, 2)->nullable()->after('discount_amount');
            $table->foreignId('program_promotion_id')->nullable()->after('final_amount')->constrained('program_promotions')->nullOnDelete();
            if (! Schema::hasColumn('registrations', 'payment_promotion_id')) {
                $table->foreignId('payment_promotion_id')->nullable()->after('payment_final_amount')->constrained('program_promotions')->nullOnDelete();
            }
            $table->string('program_promotion_title', 180)->nullable()->after('program_promotion_id');
        });
    }

    public function down(): void
    {
        Schema::table('registrations', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('program_promotion_id');
            if (Schema::hasColumn('registrations', 'payment_promotion_id')) {
                $table->dropConstrainedForeignId('payment_promotion_id');
            }
            $table->dropColumn([
                'midtrans_order_id',
                'midtrans_snap_token',
                'midtrans_redirect_url',
                'payment_status_message',
                'payment_redirect_url',
                'payment_snap_token',
                'payment_original_amount',
                'payment_discount_amount',
                'payment_final_amount',
                'original_amount',
                'discount_amount',
                'final_amount',
                'program_promotion_title',
            ]);
        });
    }
};
