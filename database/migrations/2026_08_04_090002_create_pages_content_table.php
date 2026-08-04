<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Legacy pivot between pages and contents. Production also carries a set of
     * unused foreign keys from the CMS this schema originates from
     * (webshop_id, task_id, ugyfel_id, ...); they are deliberately omitted.
     */
    public function up(): void
    {
        if (Schema::hasTable('pages_content')) {
            return;
        }

        Schema::create('pages_content', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('pages_id')->index();
            $table->unsignedBigInteger('content_id')->nullable()->index();
            $table->unsignedBigInteger('property_id')->nullable()->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pages_content');
    }
};
