<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgramPromotion extends Model
{
    use HasFactory;

    protected $fillable = [
        'program_id',
        'title',
        'description',
        'discount_type',
        'discount_value',
        'starts_at',
        'ends_at',
        'is_active',
        'badge_label',
        'terms',
    ];

    protected function casts(): array
    {
        return [
            'discount_value' => 'decimal:2',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function scopeActiveNow(Builder $query): Builder
    {
        return $query
            ->where('is_active', true)
            ->where(function (Builder $query): void {
                $query->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function (Builder $query): void {
                $query->whereNull('ends_at')->orWhere('ends_at', '>=', now());
            });
    }

    public function discountAmount(float|int|string $price): float
    {
        $price = max(0.0, (float) $price);
        $value = max(0.0, (float) $this->discount_value);

        $discount = $this->discount_type === 'percentage'
            ? $price * min($value, 100.0) / 100
            : $value;

        return min($price, round($discount, 2));
    }

    public function finalPrice(float|int|string $price): float
    {
        return max(0.0, round((float) $price - $this->discountAmount($price), 2));
    }

    public function displayBadge(): string
    {
        return $this->badge_label ?: 'Promo aktif';
    }
}
