<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Program extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'category',
        'type',
        'target_age',
        'description',
        'duration_meetings',
        'max_students',
        'price',
        'registration_fee',
        'thumbnail',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'registration_fee' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function classes(): HasMany
    {
        return $this->hasMany(CourseClass::class);
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class);
    }

    public function promotions(): HasMany
    {
        return $this->hasMany(ProgramPromotion::class);
    }

    public function activePromotions(): HasMany
    {
        return $this->promotions()
            ->activeNow()
            ->orderByDesc('starts_at')
            ->orderByDesc('discount_value');
    }

    public function currentPromotion(): ?ProgramPromotion
    {
        $promotions = $this->relationLoaded('activePromotions')
            ? $this->activePromotions
            : $this->activePromotions()->get();

        return $promotions
            ->sortByDesc(fn (ProgramPromotion $promotion): float => $promotion->discountAmount($this->price))
            ->first();
    }
}
