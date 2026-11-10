<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\BudapestDistrict;
use App\Enums\PropertyType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

/**
 * The URL of a permanently deleted property, kept so the old page answers with a
 * 301 to the page of its district instead of a 404.
 */
#[Fillable([
    'slug',
    'offer_type',
    'district',
])]
final class PropertyRedirect extends Model
{
    /**
     * Budapest districts on the Buda side of the Danube; every other district is Pest.
     *
     * @var list<BudapestDistrict>
     */
    private const array BUDA_DISTRICTS = [BudapestDistrict::I, BudapestDistrict::II, BudapestDistrict::III, BudapestDistrict::XI, BudapestDistrict::XII, BudapestDistrict::XXII];

    /**
     * Remembers the slug of a property that is being removed for good.
     */
    public static function rememberDeleted(Property $property): void
    {
        if (blank($property->slug)) {
            return;
        }

        $district = BudapestDistrict::tryFromName((string) $property->district)
            ?? BudapestDistrict::tryFromPostalCode((string) $property->postal_code);

        self::query()->updateOrCreate(['slug' => $property->slug], [
            'offer_type' => $property->type(),
            'district' => $district?->roman(),
        ]);
    }

    /**
     * Where the old page points: the warehouse category of its side of the city
     * (/budapest/budai-oldal or /budapest/pesti-oldal) when there is one, the
     * warehouse list otherwise, narrowed to the district for rentals.
     */
    public function targetUrl(): string
    {
        $type = $this->offer_type ?? PropertyType::Rent;
        $district = BudapestDistrict::tryFromName((string) $this->district);

        if ($type === PropertyType::Sale || ! $district instanceof BudapestDistrict) {
            return localized_route($type->listRoute());
        }

        $categorySlug = in_array($district, self::BUDA_DISTRICTS, true) ? 'budai-oldal' : 'pesti-oldal';

        if (Category::query()->where('slug', $categorySlug)->exists()) {
            return localized_route('budapest.category', ['category' => $categorySlug]);
        }

        return localized_route($type->listRoute(), ['district' => $district->roman()]);
    }

    protected function casts(): array
    {
        return [
            'offer_type' => PropertyType::class,
        ];
    }
}
