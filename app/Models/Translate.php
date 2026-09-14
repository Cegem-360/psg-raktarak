<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class Translate extends Model
{
    /**
     * The legacy `translates` table has no created_at / updated_at columns,
     * only its own `date` column.
     */
    public $timestamps = false;

    protected $fillable = [
        'name',
        'translated',
        'date',
        'lang',
    ];

    protected $attributes = [
        'lang' => 'EN',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'datetime',
        ];
    }
}
