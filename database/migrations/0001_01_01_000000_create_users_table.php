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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('email', 150)->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('role', ['admin', 'instructor', 'student'])->default('student');
            $table->string('avatar')->nullable();
            $table->boolean('is_active')->default(true);
            $table->rememberToken();
            $table->string('no_induk', 20)->nullable()->unique();
            $table->string('full_name', 150)->nullable();
            $table->string('place_of_birth', 100)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('sex', ['M', 'F'])->nullable();
            $table->string('religion', 30)->nullable();
            $table->string('nationality', 50)->nullable()->default('Indonesia');
            $table->string('status', 50)->nullable();
            $table->string('occupation_school', 150)->nullable();
            $table->string('mobile_phone', 20)->nullable();
            $table->string('nisn', 20)->nullable();
            $table->string('nik', 20)->nullable();
            $table->boolean('kps_receiver')->default(false);
            $table->string('no_kps', 30)->nullable();
            $table->boolean('worthy_of_pip')->default(false);
            $table->text('pip_reason')->nullable();
            $table->string('no_kip', 30)->nullable();
            $table->text('address')->nullable();
            $table->string('rt_rw', 10)->nullable();
            $table->string('postal_code', 10)->nullable();
            $table->string('village', 100)->nullable();
            $table->string('sub_district', 100)->nullable();
            $table->string('district', 100)->nullable();
            $table->string('province', 100)->nullable();
            $table->string('living_with', 100)->nullable();
            $table->string('transportation', 50)->nullable();
            $table->string('mother_name', 150)->nullable();
            $table->string('father_name', 150)->nullable();
            $table->string('instructor_position', 100)->nullable();
            $table->string('instructor_specialization', 100)->nullable();
            $table->text('instructor_bio')->nullable();
            $table->boolean('show_on_team_page')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->index('role', 'idx_users_role');
            $table->index('no_induk', 'idx_users_no_induk');
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
