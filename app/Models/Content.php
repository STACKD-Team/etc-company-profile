<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'title',
        'slug',
        'body',
        'image',
        'images',
        'meta',
        'display_order',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'images' => 'array',
            'meta' => 'array',
            'is_published' => 'boolean',
        ];
    }
}
