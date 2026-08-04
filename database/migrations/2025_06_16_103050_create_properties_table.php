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
                $table->longText('lead')->nullable();
                $table->longText('content')->nullable();
                $table->timestamp('date')->nullable()->useCurrent();
                $table->integer('ord')->nullable();
                $table->string('meta_title')->nullable();
                $table->longText('meta_title_en')->nullable();
                $table->text('meta_keywords')->nullable();
                $table->longText('meta_keywords_en')->nullable();
                $table->text('meta_description')->nullable();
                $table->longText('meta_description_en')->nullable();
                $table->string('construction_year')->nullable();
                $table->string('total_area')->nullable();
                $table->string('uzemeletetesi_dij')->nullable();
                $table->string('raktar_berleti_dij')->nullable();
                $table->string('parkolas')->nullable();
                $table->string('parkolas_dija')->nullable()->default('');
                $table->string('parkolas_dija_addons')->nullable()->default('');
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
                $table->longText('en_content')->nullable();
                $table->string('min_berleti_idoszak')->nullable();
                $table->string('min_berleti_idoszak_addons')->nullable();
                $table->string('cim_utca_addons')->nullable();
                $table->string('lang', 2)->nullable()->default('');
                $table->string('cimke')->nullable();
                $table->text('service')->nullable();
                $table->text('maps')->nullable();
                $table->string('elado_v_kiado')->nullable();
                $table->string('elado_v_kiado_addons')->nullable()->default('');
                $table->string('updated')->nullable();
                $table->string('test')->nullable()->default('');
                $table->longText('egyeb')->nullable();
                $table->string('vat')->nullable()->default('');
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
