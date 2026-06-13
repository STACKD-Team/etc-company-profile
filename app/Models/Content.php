<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    use HasFactory;

    public const TYPE_GALLERY = 'gallery';
    public const TYPE_PARTNER = 'partner';
    public const TYPE_PROFILE = 'profile';
    public const TYPE_FAQ = 'faq';
    public const TYPE_TESTIMONIAL = 'testimonial';

    public const TYPES = [
        self::TYPE_GALLERY,
        self::TYPE_PARTNER,
        self::TYPE_PROFILE,
        self::TYPE_FAQ,
        self::TYPE_TESTIMONIAL,
    ];

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
