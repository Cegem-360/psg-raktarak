<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Property;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Description('Remove backslashes from the tags field in all Property models')]
#[Signature('properties:remove-backslashes-from-tags {--dry-run : Run the command without making changes}')]
final class RemoveBackslashesFromTags extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $isDryRun = $this->option('dry-run');

        if ($isDryRun) {
            $this->info('Running in DRY RUN mode - no changes will be made');
        }

        $this->info('Starting to process Property models...');

        $properties = Property::query()->whereNotNull('tags')->get();
        /*  dd($properties->first()->tags); */
        $processedCount = 0;
        $modifiedCount = 0;

        foreach ($properties as $property) {
            $processedCount++;
            $this->line(sprintf('Processing Property ID: %s - %s', $property->id, $property->title));

            $originalTags = $property->getRawOriginal('tags');

            if (is_string($originalTags) && str_contains($originalTags, '\\')) {
                // Clean the JSON string by removing backslashes
                $cleanedTags = str_replace('\\', '', $originalTags);
                /* $cleanedTags = str_replace('\\', '', $cleanedTags); */

                $modifiedCount++;

                $this->info('  Original: '.$originalTags);
                $this->info('  Cleaned:  '.$cleanedTags);

                if (! $isDryRun) {
                    // Update the raw database value directly
                    $property->update(['tags' => $cleanedTags]);
                    $this->info('  ✅ Saved changes');
                } else {
                    $this->info('  🔍 Would be modified (dry run)');
                }
            } else {
                $this->line('  ✓ No backslashes found, skipping');
            }
        }

        $this->newLine();
        $this->info('Processing completed!');
        $this->info('Total properties processed: '.$processedCount);
        $this->info('Properties modified: '.$modifiedCount);

        if ($isDryRun) {
            $this->warn('This was a DRY RUN - no actual changes were made.');
            $this->info('Run without --dry-run flag to apply changes.');
        }

        return Command::SUCCESS;
    }
}
