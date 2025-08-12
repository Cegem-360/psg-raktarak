<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Model;

final class AboutUs extends Model
{
    protected $table = 'about_us';

    protected $fillable = [
        'language',
        'title',
        'content',
        'is_active',
    ];

    #[Scope]
    public function byLanguage($query, $language): void
    {
        $query->where('language', $language);
    }

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    #[Scope]
    protected function active($query): void
    {
        $query->where('is_active', true);
    }
}
