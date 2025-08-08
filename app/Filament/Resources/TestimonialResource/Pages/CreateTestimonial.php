<?php

declare(strict_types=1);

namespace App\Filament\Resources\TestimonialResource\Pages;

use App\Filament\Resources\TestimonialResource;
use Filament\Resources\Pages\CreateRecord;

final class CreateTestimonial extends CreateRecord
{
    protected static string $resource = TestimonialResource::class;
}
