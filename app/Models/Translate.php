<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\WithoutTimestamps;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'name',
    'translated',
    'date',
    'lang',
])]
#[WithoutTimestamps]
final class Translate extends Model
{
    protected $attributes = [
        'lang' => 'EN',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }
}
