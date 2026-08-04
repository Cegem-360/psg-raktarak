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
        if (! Schema::hasTable('galleries')) {
            Schema::create('galleries', function (Blueprint $table): void {
                $table->id();
                $table->string('path')->nullable();
                $table->integer('target_table_id')->index();
                $table->integer('ord')->default(0);
                $table->string('size', 20)->nullable();
                $table->timestamp('date')->nullable()->useCurrent()->useCurrentOnUpdate();
                $table->string('target_table', 150)->nullable();
                $table->string('path_without_size_and_ext')->nullable();
                $table->string('alt')->nullable();
                $table->integer('gallery_category_id')->default(0);
                $table->string('video_url')->nullable();
                $table->longText('images')->nullable();
                $table->timestamps();
            });
        } else {
            Schema::table('galleries', function (Blueprint $table): void {
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('galleries');
    }
};
