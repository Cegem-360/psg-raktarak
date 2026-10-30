<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The property columns came over from the old Hungarian site; they get English
 * names. The "_addons" columns hold the unit shown after a value ("EUR/m2/hó"),
 * so they become "_unit". Only the columns that exist are renamed.
 */
return new class extends Migration
{
    /** @var array<string, string> */
    private const array COLUMNS = [
        'cim_irsz' => 'postal_code',
        'cim_varos' => 'city',
        'cim_utca_addons' => 'street_type',
        'cim_utca' => 'street',
        'cim_hazszam' => 'house_number',
        'osszterulet_addons' => 'total_area_unit',
        'min_berleti_dij_addons' => 'min_rent_unit',
        'max_berleti_dij_addons' => 'max_rent_unit',
        'min_berleti_dij' => 'min_rent',
        'max_berleti_dij' => 'max_rent',
        'uzemeletetesi_dij_addons' => 'operating_fee_unit',
        'uzemeletetesi_dij' => 'operating_fee',
        'raktar_terulet_addons' => 'storage_area_unit',
        'raktar_terulet' => 'storage_area',
        'raktar_berleti_dij_addons' => 'storage_rent_unit',
        'raktar_berleti_dij' => 'storage_rent',
        'parkolas_dija_addons' => 'parking_fee_unit',
        'parkolas_dija' => 'parking_fee',
        'min_parkolas_dija_addons' => 'min_parking_fee_unit',
        'max_parkolas_dija_addons' => 'max_parking_fee_unit',
        'min_parkolas_dija' => 'min_parking_fee',
        'max_parkolas_dija' => 'max_parking_fee',
        'parkolas' => 'parking',
        'kozos_teruleti_arany_addons' => 'common_area_ratio_unit',
        'kozos_teruleti_arany' => 'common_area_ratio',
        'min_kiado_addons' => 'min_available_area_unit',
        'min_kiado' => 'min_available_area',
        'jelenleg_kiado_addons' => 'available_area_unit',
        'jelenleg_kiado' => 'available_area',
        'kodszam' => 'code',
        'min_berleti_idoszak_addons' => 'min_lease_term_unit',
        'min_berleti_idoszak' => 'min_lease_term',
        'elado_v_kiado' => 'offer_type',
        'azonosito' => 'identifier',
        'cimke' => 'label',
        'egyeb' => 'other_info',
    ];

    public function up(): void
    {
        $this->rename(self::COLUMNS);
    }

    public function down(): void
    {
        $this->rename(array_flip(self::COLUMNS));
    }

    /**
     * @param  array<string, string>  $columns
     */
    private function rename(array $columns): void
    {
        Schema::table('properties', function (Blueprint $table) use ($columns): void {
            foreach ($columns as $from => $to) {
                if (Schema::hasColumn('properties', $from)) {
                    $table->renameColumn($from, $to);
                }
            }
        });
    }
};
