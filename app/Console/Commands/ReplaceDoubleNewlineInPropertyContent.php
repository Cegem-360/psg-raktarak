<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Property;
use Illuminate\Console\Command;

final class ReplaceDoubleNewlineInPropertyContent extends Command
{
    protected $signature = 'property:replace-double-newline';

    protected $description = 'Replace all double newlines (\\n\\n) with empty string in Property content fields';

    public function handle()
    {
        $count = 0;
        Property::chunk(100, function ($properties) use (&$count) {
            foreach ($properties as $property) {
                $originalContent = $property->content;
                $originalEnContent = $property->en_content ?? '';

                $property->update([
                    'content' => preg_replace("/\n+/", '', $originalContent),
                    'en_content' => preg_replace("/\n+/", '', $originalEnContent),
                ]);

                $count++;

                $this->info("Updated Property ID: {$property->id}");

            }
        });
        $this->info("Done. Updated {$count} properties.");
    }
}
