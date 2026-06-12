<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
        'payment_gateway_id',
        'payment_redirect_url',
        'payment_snap_token',
        'midtrans_order_id',
        'midtrans_snap_token',
        'midtrans_redirect_url',
        'payment_status_message',
        'payment_expires_at',
        'original_amount',
        'discount_amount',
        'final_amount',
        'program_promotion_id',
        'program_promotion_title',
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
            'original_amount' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'final_amount' => 'decimal:2',
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

    public function programPromotion(): BelongsTo
    {
        return $this->belongsTo(ProgramPromotion::class);
    }

    public function paymentPromotion(): BelongsTo
    {
        return $this->belongsTo(ProgramPromotion::class, 'payment_promotion_id');
    }

    public function midtransNotifications(): HasMany
    {
        return $this->hasMany(MidtransNotification::class);
    }
}
