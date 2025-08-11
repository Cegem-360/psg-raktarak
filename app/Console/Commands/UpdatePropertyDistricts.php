<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Property;
use Illuminate\Console\Command;

final class UpdatePropertyDistricts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'properties:update-districts {--dry-run : Show what would be updated without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update property districts based on postal codes (Budapest only)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->info('DRY RUN MODE - No changes will be made');
            $this->line('');
        }

        // Get all Budapest properties that need district updates
        $properties = Property::where(function ($query) {
            $query->where('cim_varos', 'like', '%Budapest%')
                ->orWhere('cim_irsz', 'like', '1%');
        })
            ->whereNotNull('cim_irsz')
            ->where(function ($query) {
                $query->whereNull('district')
                    ->orWhere('district', '')
                    ->orWhere('district', 'not like', '%I%'); // Not already a Roman numeral
            })
            ->get();

        $this->info("Found {$properties->count()} properties to update");

        if ($properties->isEmpty()) {
            $this->info('No properties need district updates.');

            return;
        }

        $updated = 0;
        $skipped = 0;

        foreach ($properties as $property) {
            $district = Property::postalCodeToDistrict($property->cim_irsz);

            if ($district) {
                if ($dryRun) {
                    $this->line("Would update Property ID {$property->id}: {$property->cim_irsz} → {$district}");
                } else {
                    $property->update(['district' => $district]);
                    $this->line("Updated Property ID {$property->id}: {$property->cim_irsz} → {$district}");
                }
                $updated++;
            } else {
                $this->line("Skipped Property ID {$property->id}: Invalid postal code {$property->cim_irsz}");
                $skipped++;
            }
        }

        $this->line('');
        if ($dryRun) {
            $this->info("Would update {$updated} properties, {$skipped} skipped");
            $this->info('Run without --dry-run to apply changes');
        } else {
            $this->info("Updated {$updated} properties, {$skipped} skipped");
        }
    }
}
