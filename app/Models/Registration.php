<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Registration extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'registration_code',
        'user_id',
        'program_id',
        'class_id',
        'applicant_name',
        'applicant_email',
        'applicant_phone',
        'preferred_days',
        'preferred_time',
        'placement_test_at',
        'placement_test_result',
        'payment_method',
        'payment_status',
        'payment_amount',
        'payment_original_amount',
        'payment_discount_amount',
        'payment_final_amount',
        'payment_promotion_id',
        'payment_promotion_title',
        'payment_gateway_id',
        'payment_redirect_url',
        'payment_snap_token',
        'payment_expires_at',
        'payment_proof',
        'paid_at',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'placement_test_at' => 'datetime',
            'payment_amount' => 'decimal:2',
            'payment_original_amount' => 'decimal:2',
            'payment_discount_amount' => 'decimal:2',
            'payment_final_amount' => 'decimal:2',
            'payment_expires_at' => 'datetime',
            'paid_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function courseClass(): BelongsTo
    {
        return $this->belongsTo(CourseClass::class, 'class_id');
    }

    public function paymentPromotion(): BelongsTo
    {
        return $this->belongsTo(ProgramPromotion::class, 'payment_promotion_id');
    }
}
