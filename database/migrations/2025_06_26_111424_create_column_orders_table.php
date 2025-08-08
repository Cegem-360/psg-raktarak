<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('column_orders', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('table_id');
            $table->json('order');
            $table->unique(['user_id', 'table_id']);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('column_orders');
    }
};
