<?php

use App\Models\Content;
use App\Models\CourseClass;
use App\Models\Room;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('rooms')) {
            Schema::create('rooms', function (Blueprint $table): void {
                $table->id();
                $table->string('name', 150);
                $table->text('description')->nullable();
                $table->unsignedInteger('capacity')->nullable();
                $table->string('image', 500)->nullable();
                $table->json('facilities')->nullable();
                $table->boolean('is_active')->default(true);
                $table->integer('display_order')->default(0);
                $table->timestamps();

                $table->index('name', 'idx_rooms_name');
                $table->index('is_active', 'idx_rooms_is_active');
            });
        }

        if (Schema::hasTable('classes') && ! Schema::hasColumn('classes', 'room_id')) {
            Schema::table('classes', function (Blueprint $table): void {
                $table->foreignId('room_id')->nullable()->after('instructor_id')->constrained('rooms')->nullOnDelete();
            });
        }

        $this->migrateContentRooms();
        $this->migrateClassRooms();

        if (Schema::hasTable('classes') && Schema::hasColumn('classes', 'room')) {
            Schema::table('classes', function (Blueprint $table): void {
                $table->dropColumn('room');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('classes') && ! Schema::hasColumn('classes', 'room')) {
            Schema::table('classes', function (Blueprint $table): void {
                $table->string('room', 50)->nullable()->after('schedule_time');
            });
        }

        if (Schema::hasTable('classes') && Schema::hasColumn('classes', 'room_id')) {
            Schema::table('classes', function (Blueprint $table): void {
                $table->dropConstrainedForeignId('room_id');
            });
        }

        Schema::dropIfExists('rooms');
    }

    protected function migrateContentRooms(): void
    {
        if (! Schema::hasTable('contents')) {
            return;
        }

        Content::query()
            ->where('type', 'room')
            ->get()
            ->each(function (Content $content): void {
                Room::query()->updateOrCreate(
                    ['name' => $content->title],
                    [
                        'description' => $content->body,
                        'capacity' => $content->meta['capacity'] ?? null,
                        'image' => $content->image,
                        'facilities' => $content->meta['facility'] ?? $content->meta['facilities'] ?? [],
                        'is_active' => (bool) $content->is_published,
                        'display_order' => (int) ($content->display_order ?? 0),
                    ],
                );
            });
    }

    protected function migrateClassRooms(): void
    {
        if (! Schema::hasTable('classes') || ! Schema::hasColumn('classes', 'room') || ! Schema::hasColumn('classes', 'room_id')) {
            return;
        }

        CourseClass::query()
            ->whereNotNull('room')
            ->whereNull('room_id')
            ->get()
            ->each(function (CourseClass $class): void {
                $name = trim((string) $class->getRawOriginal('room'));

                if ($name === '') {
                    return;
                }

                $room = Room::query()->firstOrCreate(
                    ['name' => $name],
                    [
                        'description' => null,
                        'capacity' => null,
                        'facilities' => [],
                        'is_active' => true,
                        'display_order' => 0,
                    ],
                );

                $class->forceFill(['room_id' => $room->id])->save();
            });
    }
};
