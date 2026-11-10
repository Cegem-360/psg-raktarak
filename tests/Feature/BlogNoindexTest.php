<?php

declare(strict_types=1);

use App\Models\BlogPost;
use Tests\TestCase;

it('keeps noindex blog posts out of the home page and the category lists but readable', function (): void {
    /** @var TestCase $this */
    $listed = BlogPost::factory()->published()->create(['title' => 'Raktárpiaci poszt']);
    $listed->category->update(['is_active' => true]);

    $hidden = BlogPost::factory()->published()->create(['title' => 'Karácsonyi filmek', 'noindex' => true, 'blog_category_id' => $listed->blog_category_id]);

    $this->get('/')->assertSeeText('Raktárpiaci poszt')->assertDontSeeText('Karácsonyi filmek');
    $this->get(route('en.blog.category', ['category' => $listed->category->slug]))->assertSeeText('Raktárpiaci poszt')->assertDontSeeText('Karácsonyi filmek');
    $this->get(route('en.blog.show', ['post' => $hidden->slug]))->assertOk()->assertSeeHtml('<meta name="robots" content="noindex, follow">');
});
