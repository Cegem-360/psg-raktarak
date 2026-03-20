<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('properties')) {
            Schema::create('properties', function (Blueprint $table): void {
                $table->id();
                $table->string('title')->nullable();
                $table->string('status')->default('active');
                $table->text('content')->nullable();
                $table->date('date')->nullable();
                $table->integer('ord')->nullable();
                $table->string('meta_title')->nullable();
                $table->string('meta_title_en')->nullable();
                $table->string('meta_keywords')->nullable();
                $table->string('meta_keywords_en')->nullable();
                $table->text('meta_description')->nullable();
                $table->text('meta_description_en')->nullable();
                $table->string('construction_year')->nullable();
                $table->string('total_area')->nullable();
                $table->string('uzemeletetesi_dij')->nullable();
                $table->string('raktar_berleti_dij')->nullable();
                $table->string('parkolas')->nullable();
                $table->string('kozos_teruleti_arany')->nullable();
                $table->string('cim_irsz')->nullable();
                $table->string('cim_varos')->nullable();
                $table->string('district')->nullable();
                $table->string('cim_utca')->nullable();
                $table->string('cim_hazszam')->nullable();
                $table->string('maps_lat')->nullable();
                $table->string('maps_lng')->nullable();
                $table->string('azonosito')->nullable();
                $table->string('osszterulet_addons')->nullable();
                $table->string('max_berleti_dij')->nullable();
                $table->string('max_berleti_dij_addons')->nullable();
                $table->string('min_berleti_dij')->nullable();
                $table->string('min_berleti_dij_addons')->nullable();
                $table->string('raktar_terulet')->nullable();
                $table->string('raktar_terulet_addons')->nullable();
                $table->string('raktar_berleti_dij_addons')->nullable();
                $table->string('uzemeletetesi_dij_addons')->nullable();
                $table->string('min_parkolas_dija')->nullable();
                $table->string('min_parkolas_dija_addons')->nullable();
                $table->string('max_parkolas_dija')->nullable();
                $table->string('max_parkolas_dija_addons')->nullable();
                $table->string('kozos_teruleti_arany_addons')->nullable();
                $table->string('min_kiado')->nullable();
                $table->string('min_kiado_addons')->nullable();
                $table->string('jelenleg_kiado')->nullable();
                $table->string('jelenleg_kiado_addons')->nullable();
                $table->string('kodszam')->nullable();
                $table->text('en_content')->nullable();
                $table->string('min_berleti_idoszak')->nullable();
                $table->string('min_berleti_idoszak_addons')->nullable();
                $table->string('cim_utca_addons')->nullable();
                $table->string('cimke')->nullable();
                $table->text('service')->nullable();
                $table->text('maps')->nullable();
                $table->string('elado_v_kiado')->nullable();
                $table->string('updated')->nullable();
                $table->text('egyeb')->nullable();
                $table->boolean('vat')->default(false);
                $table->string('slug')->nullable();
                $table->boolean('featured')->default(false);
                $table->json('property_photos')->nullable();
                $table->timestamps();
            });
        } else {
            Schema::table('properties', function (Blueprint $table): void {
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
