<?php

namespace Tests\Feature;

use Botble\Blog\Models\Post;
use Botble\Theme\Facades\Theme;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tests\TestCase;

#[RunTestsInSeparateProcesses]
#[PreserveGlobalState(false)]
class FengShuiBlogShortcodeTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Theme::uses('martfury');
        config(['database.connections.feng_shui_test' => [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '',
        ]]);
        DB::setDefaultConnection('feng_shui_test');

        Schema::create('posts', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->text('content')->nullable();
            $table->string('status')->default('published');
            $table->unsignedBigInteger('author_id')->nullable();
            $table->string('author_type')->nullable();
            $table->string('image')->nullable();
            $table->unsignedInteger('views')->default(0);
            $table->timestamps();
        });
        Schema::create('categories', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
        });
        Schema::create('post_categories', function (Blueprint $table): void {
            $table->unsignedBigInteger('post_id');
            $table->unsignedBigInteger('category_id');
        });
        Schema::create('slugs', function (Blueprint $table): void {
            $table->id();
            $table->string('key');
            $table->string('prefix')->default('');
            $table->unsignedBigInteger('reference_id');
            $table->string('reference_type');
        });
        Schema::create('meta_boxes', function (Blueprint $table): void {
            $table->id();
            $table->string('meta_key');
            $table->text('meta_value')->nullable();
            $table->unsignedBigInteger('reference_id');
            $table->string('reference_type');
        });
    }

    protected function tearDown(): void
    {
        DB::purge('feng_shui_test');
        parent::tearDown();
    }

    public function test_automatic_slots_only_use_latest_published_posts(): void
    {
        foreach (range(1, 6) as $id) {
            $this->insertPost($id);
        }
        $this->insertPost(7, 'draft');
        $this->insertPost(8, 'pending');

        $posts = theme_get_feng_shui_posts([1 => null, 2 => '', 3 => null, 4 => null]);

        $this->assertSame([6, 5, 4, 3], $posts->pluck('id')->all());
        $this->assertSame([1, 2, 3, 4], $posts->keys()->all());
    }

    public function test_selected_slots_preserve_order_and_exclude_duplicates_and_unpublished_posts(): void
    {
        $this->insertPost(1);
        $this->insertPost(2);
        $this->insertPost(3);
        $this->insertPost(4, 'draft');

        $posts = theme_get_feng_shui_posts(['featured' => 2, 1 => null, 2 => 2, 3 => 4, 4 => 999]);

        $this->assertSame(['featured' => 2, 1 => 3], $posts->map(fn (Post $post) => $post->id)->all());
    }

    public function test_both_shortcodes_render_database_content_and_ignore_legacy_content(): void
    {
        foreach (range(1, 5) as $id) {
            $this->insertPost($id);
        }
        $source = '[BlogFengShui card_1_post_id="2" card_1_title="Old title" card_1_url="#"][/BlogFengShui]'
            . '[BlogFengShui2 featured_post_id="1" featured_title="Old featured" featured_meta="Fake views"][/BlogFengShui2]';

        $html = (string) shortcode()->compile($source, true);

        $this->assertStringContainsString('Database post 2', $html);
        $this->assertStringContainsString('Description from admin 1', $html);
        $this->assertStringContainsString('products/database-post-1.png', $html);
        $this->assertStringContainsString('database-post-1', $html);
        $this->assertStringContainsString('Database category', $html);
        $this->assertSame(4, substr_count($html, 'class="blog-feng-shui-2__side-card"'));
        $this->assertStringNotContainsString('Old title', $html);
        $this->assertStringNotContainsString('Old featured', $html);
        $this->assertStringNotContainsString('Fake views', $html);
        $this->assertStringNotContainsString('href="#"', $html);
    }

    public function test_updates_and_unpublishing_are_reflected_without_shortcode_cache(): void
    {
        $this->insertPost(1);
        $source = '[BlogFengShui card_1_post_id="1"][/BlogFengShui]';
        $this->assertStringContainsString('Database post 1', (string) shortcode()->compile($source, true));

        DB::table('posts')->where('id', 1)->update(['name' => 'Updated in admin']);
        $this->assertStringContainsString('Updated in admin', (string) shortcode()->compile($source, true));

        DB::table('posts')->where('id', 1)->update(['status' => 'draft']);
        $this->assertSame('', trim((string) shortcode()->compile($source, true)));
        $this->assertSame('', trim((string) shortcode()->compile('[BlogFengShui2][/BlogFengShui2]', true)));
    }

    public function test_missing_featured_post_promotes_an_available_article(): void
    {
        $this->insertPost(1);
        $this->insertPost(2, 'draft');
        $html = (string) shortcode()->compile('[BlogFengShui2 featured_post_id="2" article_1_post_id="1"][/BlogFengShui2]', true);

        $this->assertStringContainsString('Database post 1', $html);
        $this->assertStringNotContainsString('Database post 2', $html);
        $this->assertStringNotContainsString('class="blog-feng-shui-2__side-card"', $html);
    }

    public function test_admin_forms_offer_published_posts_instead_of_manual_article_content(): void
    {
        $this->insertPost(1);
        $this->insertPost(2, 'draft');
        $registered = shortcode()->getAll();
        $cards = $registered['BlogFengShui']['admin_config']([])->renderForm();
        $featured = $registered['BlogFengShui2']['admin_config']([])->renderForm();

        $this->assertStringContainsString('card_1_post_id', $cards);
        $this->assertStringContainsString('featured_post_id', $featured);
        $this->assertStringContainsString('article_4_post_id', $featured);
        $this->assertStringContainsString('Database post 1', $cards);
        $this->assertStringNotContainsString('Database post 2', $cards);
        $this->assertStringNotContainsString('card_1_title', $cards);
        $this->assertStringNotContainsString('featured_image', $featured);
    }

    private function insertPost(int $id, string $status = 'published'): void
    {
        DB::table('posts')->insert([
            'id' => $id, 'name' => 'Database post ' . $id, 'description' => 'Description from admin ' . $id,
            'content' => '<p>Full content from admin.</p>', 'status' => $status,
            'image' => 'products/database-post-' . $id . '.png', 'views' => $id * 10,
            'created_at' => sprintf('2026-10-%02d 10:00:00', $id), 'updated_at' => '2026-10-08 10:00:00',
        ]);
        DB::table('slugs')->insert([
            'key' => 'database-post-' . $id, 'reference_id' => $id, 'reference_type' => Post::class,
        ]);
        if (! DB::table('categories')->where('id', 1)->exists()) {
            DB::table('categories')->insert(['id' => 1, 'name' => 'Database category']);
        }
        DB::table('post_categories')->insert(['post_id' => $id, 'category_id' => 1]);
    }
}
