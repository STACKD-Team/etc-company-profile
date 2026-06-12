<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Compatibility migration kept for installations that may already know this
        // filename. Sprint 3 canonical payment snapshot fields live in
        // 2026_06_11_000001_add_sprint2_payment_snapshot_to_registrations_table.php.
    }

    public function down(): void
    {
        // No-op by design; see up().
    }
};
