<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

#[Description('Migrate property categories from view_page table to categories table')]
#[Signature('migrate:property-categories')]
final class MigratePropertyCategories extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Starting migration of property categories...');

        // Get all property categories from view_page table
        $propertyCategories = DB::table('view_page')
            ->where('type', 'property_category')
            ->get(['id', 'title']);

        if ($propertyCategories->isEmpty()) {
            $this->warn('No property categories found in view_page table.');

            return;
        }

        $this->info(sprintf('Found %d property categories to migrate.', $propertyCategories->count()));

        $migratedCount = 0;
        $skippedCount = 0;

        foreach ($propertyCategories as $category) {
            // Check if category already exists in categories table
            $existingCategory = DB::table('categories')
                ->where('name', $category->title)
                ->first();

            if ($existingCategory) {
                $this->warn(sprintf("Category '%s' already exists, skipping...", $category->title));
                $skippedCount++;

                continue;
            }

            // Insert into categories table
            DB::table('categories')->insert([
                'name' => $category->title,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->info(sprintf("Migrated: '%s'", $category->title));
            $migratedCount++;
        }

        $this->info('Migration completed!');
        $this->info(sprintf('Migrated: %d categories', $migratedCount));
        $this->info(sprintf('Skipped: %d categories', $skippedCount));
    }
}
