<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

final class SyncPropertyCategoryRelationships extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:property-category-relationships {--clear : Clear existing relationships before syncing}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync property-category relationships from view_page content_json to category_property table';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting sync of property-category relationships...');

        // Check if we should clear existing relationships
        if ($this->option('clear')) {
            $this->warn('Clearing existing relationships...');
            DB::table('category_property')->truncate();
            $this->info('Existing relationships cleared.');
        }

        // Get all property categories from view_page table with their content_json
        $propertyCategories = DB::table('view_page')
            ->where('type', 'property_category')
            ->whereNotNull('content_json')
            ->where('content_json', '!=', '')
            ->get(['id', 'title', 'content_json']);

        if ($propertyCategories->isEmpty()) {
            $this->warn('No property categories with content_json found in view_page table.');

            return Command::SUCCESS;
        }

        $this->info("Found {$propertyCategories->count()} property categories with content data.");

        $relationshipCount = 0;
        $skippedCount = 0;
        $errorCount = 0;

        foreach ($propertyCategories as $viewPageCategory) {
            // Find corresponding category in categories table by name
            $category = DB::table('categories')
                ->where('name', $viewPageCategory->title)
                ->first();

            if (! $category) {
                $this->error("Category '{$viewPageCategory->title}' not found in categories table. Run migrate:property-categories first.");
                $errorCount++;

                continue;
            }

            $this->info("Processing category: '{$category->name}' (ID: {$category->id})");

            // Decode the JSON content
            $propertyIds = json_decode($viewPageCategory->content_json, true);
            if (! is_array($propertyIds)) {
                $this->warn("  Invalid JSON content for category '{$category->name}', skipping...");
                $skippedCount++;

                continue;
            }

            if (empty($propertyIds)) {
                $this->info("  No property IDs found for category '{$category->name}'");

                continue;
            }

            $this->info('  Processing '.count($propertyIds).' property relationships...');

            foreach ($propertyIds as $propertyId) {
                if (! is_numeric($propertyId)) {
                    $this->warn("    Invalid property ID '{$propertyId}', skipping...");

                    continue;
                }

                $propertyId = (int) $propertyId;

                // Check if property exists
                $propertyExists = DB::table('properties')
                    ->where('id', $propertyId)
                    ->exists();

                if (! $propertyExists) {
                    $this->warn("    Property ID {$propertyId} does not exist, skipping...");

                    continue;
                }

                // Check if relationship already exists
                $relationshipExists = DB::table('category_property')
                    ->where('category_id', $category->id)
                    ->where('property_id', $propertyId)
                    ->exists();

                if ($relationshipExists) {
                    $this->comment("    Relationship already exists: Category {$category->id} - Property {$propertyId}");
                    $skippedCount++;

                    continue;
                }

                // Create relationship
                DB::table('category_property')->insert([
                    'category_id' => $category->id,
                    'property_id' => $propertyId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $relationshipCount++;
            }

            $this->info("  ✓ Processed relationships for '{$category->name}'");
        }

        $this->info('Sync completed!');
        $this->info("Created relationships: {$relationshipCount}");
        $this->info("Skipped relationships: {$skippedCount}");

        if ($errorCount > 0) {
            $this->error("Errors encountered: {$errorCount}");

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
