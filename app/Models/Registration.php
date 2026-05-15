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
        'payment_amount',
        'payment_gateway_id',
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
}
