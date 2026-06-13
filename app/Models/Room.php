<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Room extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'capacity',
        'image',
        'facilities',
        'is_active',
        'display_order',
    ];

    protected function casts(): array
    {
        return [
            'capacity' => 'integer',
            'facilities' => 'array',
            'is_active' => 'boolean',
            'display_order' => 'integer',
        ];
    }

    public function classes(): HasMany
    {
        return $this->hasMany(CourseClass::class);
    }
}
