<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;

class CourseClass extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'classes';

    protected $fillable = [
        'program_id',
        'instructor_id',
        'room_id',
        'name',
        'schedule_days',
        'schedule_time',
        'room',
        'start_date',
        'end_date',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function setRoomAttribute(?string $value): void
    {
        if (Schema::hasColumn($this->getTable(), 'room')) {
            $this->attributes['room'] = $value;
        }

        if (! Schema::hasTable('rooms') || ! Schema::hasColumn($this->getTable(), 'room_id')) {
            return;
        }

        $name = trim((string) $value);

        if ($name === '') {
            $this->attributes['room_id'] = null;

            return;
        }

        $this->attributes['room_id'] = Room::query()->firstOrCreate(['name' => $name])->getKey();
    }

    public function getRoomLabelAttribute(): ?string
    {
        $room = $this->relationLoaded('room') ? $this->getRelation('room') : $this->room()->first();

        return $room?->name ?? $this->attributes['room'] ?? null;
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class, 'class_id');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class, 'class_id');
    }
}
