<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class BlogPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'featured_image',
        'blog_category_id',
        'user_id',
        'is_published',
        'published_at',
        'meta_data',
        'views_count',
        'link',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
        'meta_data' => 'array',
        'views_count' => 'integer',
    ];

    // Relationships
    public function category(): BelongsTo
    {
        return $this->belongsTo(BlogCategory::class, 'blog_category_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Scopes
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function scopeByCategory(Builder $query, int $categoryId): Builder
    {
        return $query->where('blog_category_id', $categoryId);
    }

    // Accessors
    public function getReadingTimeAttribute(): string
    {
        $wordCount = str_word_count(strip_tags($this->content));
        $minutes = ceil($wordCount / 200); // Average reading speed

        return $minutes.' perc olvasás';
    }

    public function getStatusAttribute(): string
    {
        if ($this->is_published && $this->published_at && $this->published_at <= now()) {
            return 'published';
        }

        if ($this->is_published && $this->published_at && $this->published_at > now()) {
            return 'scheduled';
        }

        return 'draft';
    }

    public function incrementViews(): void
    {
        $this->increment('views_count');
    }

    public function publish(): void
    {
        $this->update([
            'is_published' => true,
            'published_at' => now(),
        ]);
    }

    public function unpublish(): void
    {
        $this->update([
            'is_published' => false,
            'published_at' => null,
        ]);
    }

    #[Scope]
    protected function draft(Builder $query): void
    {
        $query->where('is_published', false);
    }
}
