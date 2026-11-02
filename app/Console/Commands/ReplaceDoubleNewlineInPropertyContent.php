<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Property;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Description('Replace all double newlines (\\n\\n) with empty string in Property content fields')]
#[Signature('property:replace-double-newline')]
final class ReplaceDoubleNewlineInPropertyContent extends Command
{
    public function handle(): void
    {
        $count = 0;
        Property::query()->chunk(100, function ($properties) use (&$count): void {
            foreach ($properties as $property) {
                $originalContent = $property->content;
                $originalEnContent = $property->en_content ?? '';

                $property->update([
                    'content' => preg_replace("/\n+/", '', $originalContent),
                    'en_content' => preg_replace("/\n+/", '', $originalEnContent),
                ]);

                $count++;

                $this->info('Updated Property ID: '.$property->id);

            }
        });
        $this->info(sprintf('Done. Updated %d properties.', $count));
    }
}
