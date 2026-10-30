<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

/**
 * Whether a property is offered for rent or for sale (the `offer_type` column).
 */
enum PropertyType: string implements HasLabel
{
    case Rent = 'kiado-raktar';
    case Sale = 'elado-raktar';

    public function getLabel(): string
    {
        return match ($this) {
            self::Rent => 'Kiadó',
            self::Sale => 'Eladó',
        };
    }

    /**
     * The route of the office list this type belongs to.
     */
    public function listRoute(): string
    {
        return match ($this) {
            self::Rent => 'kiado-raktarak',
            self::Sale => 'elado-raktarak',
        };
    }

    /**
     * The title of that office list, as shown in the breadcrumbs.
     */
    public function listTitle(): string
    {
        return match ($this) {
            self::Rent => __('Warehouses for rent'),
            self::Sale => __('Warehouses for Sale'),
        };
    }

    /**
     * The public page route of a property of this type.
     */
    public function showRoute(): string
    {
        return match ($this) {
            self::Rent => 'properties.show',
            self::Sale => 'properties.show-for-sale',
        };
    }
}
