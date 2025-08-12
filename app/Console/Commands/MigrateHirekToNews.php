<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\News;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final class MigrateHirekToNews extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'hirek:migrate-to-news {--dry-run : Run in dry-run mode without actual database changes}';

    /**
     * The console command description.
     */
    protected $description = 'Migrate content from Hírek page to News table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');

        if ($isDryRun) {
            $this->info('Running in DRY-RUN mode - no database changes will be made');
        }

        // 1. Get the Hírek page
        $hirekPage = DB::table('pages')->where('title', 'Hírek')->first();

        if (! $hirekPage) {
            $this->error('Hírek page not found!');

            return Command::FAILURE;
        }

        $this->info("Found Hírek page with ID: {$hirekPage->id}");

        // 2. Parse the content_json to get content IDs
        $contentIds = json_decode($hirekPage->content_json, true);

        if (! is_array($contentIds)) {
            $this->error('Invalid content_json format!');

            return Command::FAILURE;
        }

        $this->info('Found '.count($contentIds).' content IDs to migrate');

        // 3. Get all content records
        $contents = DB::table('contents')
            ->whereIn('id', $contentIds)
            ->where('status', 'active')
            ->whereNotNull('title')
            ->where('title', '!=', '')
            ->get();

        $this->info('Found '.$contents->count().' active content records');

        // 4. Check for existing migrations
        $existingCount = DB::table('news')
            ->whereRaw("JSON_EXTRACT(meta_data, '$.original_content_id') IS NOT NULL")
            ->count();

        if ($existingCount > 0) {
            if (! $this->confirm("Found {$existingCount} already migrated news items. Continue anyway?")) {
                return self::FAILURE;
            }
        }

        // 5. Migrate each content to news
        $migratedCount = 0;
        $skippedCount = 0;

        $progressBar = $this->output->createProgressBar($contents->count());
        $progressBar->start();

        foreach ($contents as $content) {
            // Check if already migrated
            $alreadyExists = DB::table('news')
                ->whereRaw("JSON_EXTRACT(meta_data, '$.original_content_id') = ?", [$content->id])
                ->exists();

            if ($alreadyExists) {
                $skippedCount++;
                $progressBar->advance();

                continue;
            }

            // Prepare data for news table
            $newsData = [
                'title' => $content->title,
                'slug' => $this->generateSlug($content->title, $content->id),
                'excerpt' => $this->extractExcerpt($content->lead ?? ''),
                'content' => $content->content ?? '',
                'featured_image' => null, // We'll extract this later if needed
                'news_category_id' => null,
                'user_id' => 1, // Default admin user
                'is_published' => $content->status === 'active',
                'published_at' => $content->date,
                'meta_data' => json_encode([
                    'meta_title' => $content->meta_title ?? '',
                    'meta_description' => $content->meta_description ?? '',
                    'meta_keywords' => $content->meta_keywords ?? '',
                    'source_link' => $content->link ?? '',
                    'original_content_id' => $content->id,
                ]),
                'source' => $content->link ?? '',
                'views_count' => 0,
                'priority' => 2, // Normal priority
                'is_breaking' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (! $isDryRun) {
                try {
                    DB::table('news')->insert($newsData);
                    $migratedCount++;
                } catch (Exception $e) {
                    $this->error("Failed to migrate content ID {$content->id}: ".$e->getMessage());

                    continue;
                }
            } else {
                $migratedCount++; // Count for dry run
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine();

        // 6. Summary
        if ($isDryRun) {
            $this->info('DRY-RUN RESULTS:');
            $this->table(
                ['Metric', 'Count'],
                [
                    ['Total content records found', $contents->count()],
                    ['Would be migrated', $migratedCount],
                    ['Would be skipped (already exists)', $skippedCount],
                ]
            );
        } else {
            $this->info('MIGRATION COMPLETED:');
            $this->table(
                ['Metric', 'Count'],
                [
                    ['Total content records found', $contents->count()],
                    ['Successfully migrated', $migratedCount],
                    ['Skipped (already exists)', $skippedCount],
                ]
            );

            // Show some examples
            $migratedNews = DB::table('news')
                ->whereRaw("JSON_EXTRACT(meta_data, '$.original_content_id') IS NOT NULL")
                ->orderBy('published_at', 'desc')
                ->limit(5)
                ->get(['id', 'title', 'published_at']);

            if ($migratedNews->count() > 0) {
                $this->info("\nLast 5 migrated news articles:");
                $this->table(
                    ['ID', 'Title', 'Published At'],
                    $migratedNews->map(fn ($news) => [
                        $news->id,
                        Str::limit($news->title, 50),
                        $news->published_at,
                    ])->toArray()
                );
            }
        }

        return Command::SUCCESS;
    }

    /**
     * Generate a slug for the news article
     */
    private function generateSlug(string $title, int $contentId): string
    {
        // Basic Hungarian character replacement and slug generation
        $slug = mb_strtolower($title);
        $slug = str_replace(
            ['á', 'é', 'í', 'ó', 'ö', 'ő', 'ú', 'ü', 'ű'],
            ['a', 'e', 'i', 'o', 'o', 'o', 'u', 'u', 'u'],
            $slug
        );
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
        $slug = preg_replace('/\s+/', '-', mb_trim($slug));
        $slug = preg_replace('/-+/', '-', $slug);
        $slug = mb_trim($slug, '-');

        // Add content ID to ensure uniqueness
        return Str::limit($slug, 50, '');
    }

    /**
     * Extract excerpt from HTML content
     */
    private function extractExcerpt(string $htmlContent): string
    {
        // Remove HTML tags
        $text = strip_tags($htmlContent);
        // Clean up whitespace
        $text = preg_replace('/\s+/', ' ', mb_trim($text));

        // Limit length
        return Str::limit($text, 200);
    }
}
