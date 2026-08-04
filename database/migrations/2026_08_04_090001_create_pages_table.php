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
        if (Schema::hasTable('pages')) {
            return;
        }

        Schema::create('pages', function (Blueprint $table): void {
            $table->id();
            $table->string('title');
            $table->string('url');
            $table->integer('ord')->default(0);
            $table->string('template', 150);
            $table->unsignedBigInteger('parent_id')->default(0);
            $table->string('need_login', 25)->nullable()->default('')->comment('Permission name: admin, super-admin, user or empty');
            $table->integer('show_menu')->nullable()->default(0);
            $table->timestamp('date')->nullable()->useCurrent()->useCurrentOnUpdate();
            $table->string('type', 100)->nullable();
            $table->longText('content_json')->nullable();
            $table->string('title_url')->nullable();
            $table->integer('sow_just_super_admin')->default(0);
            $table->integer('content_category_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
