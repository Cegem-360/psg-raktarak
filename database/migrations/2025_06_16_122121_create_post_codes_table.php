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
        // The legacy import had no primary key on post_codes; a freshly created
        // table (see the previous migration) already has one.
        if (Schema::hasTable('post_codes') && ! Schema::hasColumn('post_codes', 'id')) {
            Schema::table('post_codes', function (Blueprint $table): void {
                $table->id();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
