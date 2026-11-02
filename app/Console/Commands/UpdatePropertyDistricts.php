<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\BudapestDistrict;
use App\Models\Property;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Contracts\Database\Query\Builder;

#[Description('Update property districts based on postal codes (Budapest only)')]
#[Signature('properties:update-districts {--dry-run : Show what would be updated without making changes}')]
final class UpdatePropertyDistricts extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->info('DRY RUN MODE - No changes will be made');
            $this->line('');
        }

        // Get all Budapest properties that need district updates
        $properties = Property::query()->where(function (Builder $query): void {
            $query->where('city', 'like', '%Budapest%')
                ->orWhere('postal_code', 'like', '1%');
        })
            ->whereNotNull('postal_code')
            ->where(function (Builder $query): void {
                $query->whereNull('district')
                    ->orWhere('district', '')
                    ->orWhere('district', 'not like', '%I%'); // Not already a Roman numeral
            })
            ->get();

        $this->info(sprintf('Found %s properties to update', $properties->count()));

        if ($properties->isEmpty()) {
            $this->info('No properties need district updates.');

            return;
        }

        $updated = 0;
        $skipped = 0;

        foreach ($properties as $property) {
            $district = BudapestDistrict::tryFromPostalCode((string) $property->postal_code)?->roman();

            if ($district) {
                if ($dryRun) {
                    $this->line(sprintf('Would update Property ID %s: %s → %s', $property->id, $property->postal_code, $district));
                } else {
                    $property->update(['district' => $district]);
                    $this->line(sprintf('Updated Property ID %s: %s → %s', $property->id, $property->postal_code, $district));
                }

                $updated++;
            } else {
                $this->line(sprintf('Skipped Property ID %s: Invalid postal code %s', $property->id, $property->postal_code));
                $skipped++;
            }
        }

        $this->line('');
        if ($dryRun) {
            $this->info(sprintf('Would update %d properties, %d skipped', $updated, $skipped));
            $this->info('Run without --dry-run to apply changes');
        } else {
            $this->info(sprintf('Updated %d properties, %d skipped', $updated, $skipped));
        }
    }
}
