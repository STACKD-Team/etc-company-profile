<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatbotLog extends Model
{
    use HasFactory;

    public const UPDATED_AT = null;

    protected $fillable = [
        'session_id',
        'user_id',
        'user_message',
        'bot_response',
        'intent',
        'is_helpful',
    ];

    protected function casts(): array
    {
        return [
            'is_helpful' => 'boolean',
            'created_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
