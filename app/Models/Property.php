<?php

declare(strict_types=1);

namespace App\Models;

use App\Observers\PropertyObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

#[ObservedBy(PropertyObserver::class)]
final class Property extends Model
{
    protected $guarded = [];

    protected $fillable = [

        'title',
        'status',
        'content',
        'date',
        'ord',
        'meta_title',
        'meta_title_en',
        'meta_keywords',
        'meta_keywords_en',
        'meta_description',
        'meta_description_en',
        'construction_year',
        'total_area',
        'uzemeletetesi_dij',
        'raktar_berleti_dij',
        'parkolas',
        'kozos_teruleti_arany',
        'cim_irsz',
        'cim_varos',
        'district',
        'cim_utca',
        'cim_hazszam',
        'maps_lat',
        'maps_lng',
        'azonosito',
        'osszterulet_addons',
        'max_berleti_dij',
        'max_berleti_dij_addons',
        'min_berleti_dij',
        'min_berleti_dij_addons',
        'raktar_terulet',
        'raktar_terulet_addons',
        'raktar_berleti_dij_addons',
        'uzemeletetesi_dij_addons',
        'min_parkolas_dija',
        'min_parkolas_dija_addons',
        'max_parkolas_dija',
        'max_parkolas_dija_addons',
        'kozos_teruleti_arany_addons',
        'min_kiado',
        'min_kiado_addons',
        'jelenleg_kiado',
        'jelenleg_kiado_addons',
        'kodszam',
        'en_content',
        'min_berleti_idoszak',
        'min_berleti_idoszak_addons',
        'cim_utca_addons',
        'cimke',
        'service',
        'maps',
        'elado_v_kiado',
        'updated',
        'egyeb',
        'vat',
        'slug',
        'featured',
        'property_photos',
        'created_at',
        'updated_at',
    ];

    public static function countByDistrict(): array
    {
        // Kerületek római számmal
        $romanNumerals = [
            1 => 'I',
            2 => 'II',
            3 => 'III',
            4 => 'IV',
            5 => 'V',
            6 => 'VI',
            7 => 'VII',
            8 => 'VIII',
            9 => 'IX',
            10 => 'X',
            11 => 'XI',
            12 => 'XII',
            13 => 'XIII',
            14 => 'XIV',
            15 => 'XV',
            16 => 'XVI',
            17 => 'XVII',
            18 => 'XVIII',
            19 => 'XIX',
            20 => 'XX',
            21 => 'XXI',
            22 => 'XXII',
            23 => 'XXIII',
        ];

        $result = [];

        foreach (array_keys($romanNumerals) as $num) {
            $count = self::where(function ($q) use ($num): void {
                $q->rent()->active()->budapestOnly()->inDistrict((string) $num);
            })->count();

            $result[$num] = $count;
        }

        return $result;
    }

    /**
     * Convert Budapest postal code to district Roman numeral
     */
    public static function postalCodeToDistrict(string $postalCode): ?string
    {
        // Check if it's a valid 4-digit Budapest postal code
        if (mb_strlen($postalCode) !== 4 || ! ctype_digit($postalCode)) {
            return null;
        }

        $code = (int) $postalCode;

        // Check if it's a Budapest postal code (1000-1999)
        if ($code < 1000 || $code > 1999) {
            return null;
        }

        // Calculate district number using the mathematical formula
        $districtNum = floor($code / 10) - 100;

        // Roman numerals mapping
        $romanNumerals = [
            1 => 'I',
            2 => 'II',
            3 => 'III',
            4 => 'IV',
            5 => 'V',
            6 => 'VI',
            7 => 'VII',
            8 => 'VIII',
            9 => 'IX',
            10 => 'X',
            11 => 'XI',
            12 => 'XII',
            13 => 'XIII',
            14 => 'XIV',
            15 => 'XV',
            16 => 'XVI',
            17 => 'XVII',
            18 => 'XVIII',
            19 => 'XIX',
            20 => 'XX',
            21 => 'XXI',
            22 => 'XXII',
            23 => 'XXIII',
        ];

        return $romanNumerals[$districtNum] ?? null;
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function getAddressFormated(): string
    {
        $address = mb_trim(sprintf('%s %s, %s %s %s', $this->cim_irsz, $this->cim_varos, $this->cim_utca, $this->cim_utca_addons, $this->cim_hazszam));
        $rent = __('Rental fee');
        $address .=
         '<br><strong>'.$rent.':</strong> '.
          $this->min_berleti_dij
          .($this->max_berleti_dij && $this->max_berleti_dij !== $this->min_berleti_dij ? ' - '.$this->max_berleti_dij.' '.$this->min_berleti_dij_addons : ' '.$this->min_berleti_dij_addons)
          .'<br><strong>'.__('Operating Fee').': </strong>'.$this->uzemeletetesi_dij.' '.$this->uzemeletetesi_dij_addons;

        return $address ?: null;
    }

    public function getAddressFormatedForSale(): string
    {
        return sprintf('%s %s,<br><strong>', $this->cim_irsz, $this->cim_varos).__('Total Area').sprintf(':</strong> %s m²<br><strong>', $this->total_area).__('Price').sprintf(':</strong> %s %s', $this->min_berleti_dij, $this->min_berleti_dij_addons);
    }

    public function getFirstImageUrl(): ?string
    {
        return Storage::url(collect($this->property_photos)->first());
    }

    public function isRent(): bool
    {
        return $this->elado_v_kiado === 'kiado-raktar';
    }

    public function isSale(): bool
    {
        return $this->elado_v_kiado === 'elado-raktar';
    }

    protected function casts()
    {
        return [
            'updated_at' => 'datetime',
            'created_at' => 'datetime',
            'featured' => 'boolean',
            'vat' => 'boolean',
            'property_photos' => 'array',
        ];
    }

    #[Scope]
    protected function byCategory(Builder $query, string $category): void
    {
        $query->whereHas('categories', function (Builder $q) use ($category): void {
            $q->where('name', $category);
        });
    }

    #[Scope]
    protected function featured(Builder $query): void
    {
        $query->where('featured', true);
    }

    #[Scope]
    protected function active(Builder $query): void
    {
        $query->where('status', 'active');
    }

    #[Scope]
    protected function inactive(Builder $query): void
    {
        $query->where('status', 'inactive');
    }

    #[Scope]
    protected function sale(Builder $query): void
    {
        $query->where('elado_v_kiado', 'elado-raktar');
    }

    #[Scope]
    protected function rent(Builder $query): void
    {
        $query->where('elado_v_kiado', 'kiado-raktar');
    }

    #[Scope]
    protected function agglomeration(Builder $query): void
    {
        $query->where('cim_varos', '!=', 'Budapest');
    }

    #[Scope]
    protected function budapestOnly(Builder $query): void
    {
        $query->where(function ($q): void {
            $q->where('cim_varos', 'like', '%Budapest%')
                ->orWhere(function ($subQ): void {
                    $subQ->where('cim_irsz', 'like', '1%');
                });
        });
    }

    #[Scope]
    protected function inDistrict(Builder $query, string $district): void
    {
        $districtNum = (int) $district;

        // Roman numerals mapping
        $romanNumerals = [
            1 => 'I',
            2 => 'II',
            3 => 'III',
            4 => 'IV',
            5 => 'V',
            6 => 'VI',
            7 => 'VII',
            8 => 'VIII',
            9 => 'IX',
            10 => 'X',
            11 => 'XI',
            12 => 'XII',
            13 => 'XIII',
            14 => 'XIV',
            15 => 'XV',
            16 => 'XVI',
            17 => 'XVII',
            18 => 'XVIII',
            19 => 'XIX',
            20 => 'XX',
            21 => 'XXI',
            22 => 'XXII',
            23 => 'XXIII',
        ];

        $romanDistrict = $romanNumerals[$districtNum] ?? $district;

        $query->where(function ($q) use ($romanDistrict, $district, $districtNum): void {
            // District oszlopban keresés
            $q->where('district', $romanDistrict)
                ->orWhere('district', $district);

            // Irányítószám alapú keresés - matematikai megoldás
            if ($districtNum >= 1 && $districtNum <= 23) {
                $q->orWhere(function ($subQ) use ($districtNum): void {
                    // Irányítószám → szám → osztás 10-zel → kivonás 100
                    // Például: 1051 → 1051/10=105 → 105-100=5 → 5. kerület
                    $subQ->whereRaw('CHAR_LENGTH(cim_irsz) = 4')
                        ->whereRaw('CAST(cim_irsz AS UNSIGNED) > 999')
                        ->whereRaw('CAST(cim_irsz AS UNSIGNED) < 10000')
                        ->whereRaw('FLOOR(CAST(cim_irsz AS UNSIGNED) / 10) - 100 = ?', [$districtNum]);
                });
            }
        });
    }

    #[Scope]
    protected function inDistricts(Builder $query, array $districts): void
    {
        $query->where(function ($q) use ($districts): void {
            foreach ($districts as $district) {
                $q->orWhere(function ($subQ) use ($district): void {
                    $districtNum = (int) $district;

                    // Roman numerals mapping
                    $romanNumerals = [
                        1 => 'I',
                        2 => 'II',
                        3 => 'III',
                        4 => 'IV',
                        5 => 'V',
                        6 => 'VI',
                        7 => 'VII',
                        8 => 'VIII',
                        9 => 'IX',
                        10 => 'X',
                        11 => 'XI',
                        12 => 'XII',
                        13 => 'XIII',
                        14 => 'XIV',
                        15 => 'XV',
                        16 => 'XVI',
                        17 => 'XVII',
                        18 => 'XVIII',
                        19 => 'XIX',
                        20 => 'XX',
                        21 => 'XXI',
                        22 => 'XXII',
                        23 => 'XXIII',
                    ];

                    $romanDistrict = $romanNumerals[$districtNum] ?? $district;

                    // District oszlopban keresés
                    $subQ->where('district', $romanDistrict)
                        ->orWhere('district', $district);

                    // Irányítószám alapú keresés - matematikai megoldás
                    if ($districtNum >= 1 && $districtNum <= 23) {
                        $subQ->orWhere(function ($postalQ) use ($districtNum): void {
                            // Irányítószám → szám → osztás 10-zel → kivonás 100
                            // Például: 1051 → 1051/10=105 → 105-100=5 → 5. kerület
                            $postalQ->whereRaw('CHAR_LENGTH(cim_irsz) = 4')
                                ->whereRaw('CAST(cim_irsz AS UNSIGNED) > 999')
                                ->whereRaw('CAST(cim_irsz AS UNSIGNED) < 10000')
                                ->whereRaw('FLOOR(CAST(cim_irsz AS UNSIGNED) / 10) - 100 = ?', [$districtNum]);
                        });
                    }
                });
            }
        });
    }

    #[Scope]
    protected function searchText(Builder $query, string $search): void
    {
        $query->where(function (Builder $subQ) use ($search): void {
            $subQ->where('title', 'like', '%'.$search.'%')
                ->orWhere('content', 'like', '%'.$search.'%')
                ->orWhere('kodszam', 'like', '%'.$search.'%')
                ->orWhereHas('tags', function (Builder $q) use ($search): void {
                    $q->where('name', 'like', '%'.$search.'%');
                })
                ->orWhereHas('services', function (Builder $q) use ($search): void {
                    $q->where('name', 'like', '%'.$search.'%');
                });
        });
    }

    #[Scope]
    protected function byOfficeName(Builder $query, string $officeName): void
    {
        $query->where('title', 'like', '%'.$officeName.'%');
    }

    #[Scope]
    protected function areaRange(Builder $query, ?int $minArea = null, ?int $maxArea = null): void
    {
        if ($minArea && $maxArea) {
            $query->whereBetween('total_area', [$minArea, $maxArea]);
        } elseif ($minArea) {
            $query->where('total_area', '>=', $minArea);
        } elseif ($maxArea) {
            $query->where('total_area', '<=', $maxArea);
        }
    }

    #[Scope]
    protected function priceRange(Builder $query, ?int $minPrice = null, ?int $maxPrice = null): void
    {
        if ($minPrice && $maxPrice) {
            $query->whereBetween('max_berleti_dij', [$minPrice, $maxPrice]);
        } elseif ($minPrice) {
            $query->where('max_berleti_dij', '>=', $minPrice);
        } elseif ($maxPrice) {
            $query->where('max_berleti_dij', '<=', $maxPrice);
        }
    }

    protected function slug(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ?: Str::slug($this->title),
            set: fn ($value) => $value ?: Str::slug($this->title)
        );
    }

    /**
     * Automatically set district when postal code is set
     */
    protected function cimIrsz(): Attribute
    {
        return Attribute::make(
            set: function ($value) {
                // Auto-set district if it's a Budapest postal code
                if ($value && $this->cim_varos && str_contains($this->cim_varos, 'Budapest')) {
                    $district = self::postalCodeToDistrict($value);
                    if ($district) {
                        $this->attributes['district'] = $district;
                    }
                }

                return $value;
            }
        );
    }
}
