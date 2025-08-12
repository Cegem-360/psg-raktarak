<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class ContactPage extends Model
{
    use HasFactory;

    protected $fillable = [
        'language',
        'content',
        'image',
    ];

    /**
     * Get the active contact page by language.
     */
    public static function getActiveByLanguage(string $language = 'hu'): ?self
    {
        return self::where('language', $language)->first();
    }

    protected function casts(): array
    {
        return [
            'content' => 'string',
        ];
    }
}
