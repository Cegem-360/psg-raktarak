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
     * Mirrors the legacy production table; it has no created_at/updated_at.
     */
    public function up(): void
    {
        if (Schema::hasTable('translates')) {
            return;
        }

        Schema::create('translates', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->default('')->index();
            $table->string('translated')->default('');
            $table->timestamp('date')->nullable()->useCurrent();
            $table->string('lang', 2)->nullable()->default('EN');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('translates');
    }
};
