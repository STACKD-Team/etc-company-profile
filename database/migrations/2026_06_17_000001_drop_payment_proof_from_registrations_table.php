<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registrations', function (Blueprint $table): void {
            if (Schema::hasColumn('registrations', 'payment_proof')) {
                $table->dropColumn('payment_proof');
            }
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE registrations MODIFY payment_method ENUM('qris','bank_transfer','virtual_account','ewallet') NULL");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE registrations MODIFY payment_method ENUM('qris','bank_transfer','virtual_account','ewallet','manual') NULL");
        }

        Schema::table('registrations', function (Blueprint $table): void {
            if (! Schema::hasColumn('registrations', 'payment_proof')) {
                $table->string('payment_proof')->nullable()->after('payment_gateway_id');
            }
        });
    }
};
