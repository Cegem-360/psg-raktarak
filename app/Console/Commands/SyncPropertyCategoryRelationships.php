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

        $this->info(sprintf('Found %d property categories with content data.', $propertyCategories->count()));

        $relationshipCount = 0;
        $skippedCount = 0;
        $errorCount = 0;

        foreach ($propertyCategories as $viewPageCategory) {
            // Find corresponding category in categories table by name
            $category = DB::table('categories')
                ->where('name', $viewPageCategory->title)
                ->first();

            if (! $category) {
                $this->error(sprintf("Category '%s' not found in categories table. Run migrate:property-categories first.", $viewPageCategory->title));
                $errorCount++;

                continue;
            }

            $this->info(sprintf("Processing category: '%s' (ID: %s)", $category->name, $category->id));

            // Decode the JSON content
            $propertyIds = json_decode($viewPageCategory->content_json, true);
            if (! is_array($propertyIds)) {
                $this->warn(sprintf("  Invalid JSON content for category '%s', skipping...", $category->name));
                $skippedCount++;

                continue;
            }

            if ($propertyIds === []) {
                $this->info(sprintf("  No property IDs found for category '%s'", $category->name));

                continue;
            }

            $this->info('  Processing '.count($propertyIds).' property relationships...');

            foreach ($propertyIds as $propertyId) {
                if (! is_numeric($propertyId)) {
                    $this->warn(sprintf("    Invalid property ID '%s', skipping...", $propertyId));

                    continue;
                }

                $propertyId = (int) $propertyId;

                // Check if property exists
                $propertyExists = DB::table('properties')
                    ->where('id', $propertyId)
                    ->exists();

                if (! $propertyExists) {
                    $this->warn(sprintf('    Property ID %d does not exist, skipping...', $propertyId));

                    continue;
                }

                // Check if relationship already exists
                $relationshipExists = DB::table('category_property')
                    ->where('category_id', $category->id)
                    ->where('property_id', $propertyId)
                    ->exists();

                if ($relationshipExists) {
                    $this->comment(sprintf('    Relationship already exists: Category %s - Property %d', $category->id, $propertyId));
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

            $this->info(sprintf("  ✓ Processed relationships for '%s'", $category->name));
        }

        $this->info('Sync completed!');
        $this->info('Created relationships: ' . $relationshipCount);
        $this->info('Skipped relationships: ' . $skippedCount);

        if ($errorCount > 0) {
            $this->error('Errors encountered: ' . $errorCount);

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
