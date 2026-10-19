<?php

namespace Tests\Feature;

use Botble\Theme\Facades\Theme;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tests\TestCase;

#[RunTestsInSeparateProcesses]
#[PreserveGlobalState(false)]
class BlogFengShui3ShortcodeTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Theme::uses('martfury');
    }

    public function test_both_designs_have_distinct_registrations_and_admin_fields(): void
    {
        $registered = shortcode()->getAll();
        $this->assertArrayHasKey('BlogFengShui3', $registered);
        $this->assertArrayHasKey('BlogFengShui4', $registered);
        $form = $registered['BlogFengShui3']['admin_config']([]);
        foreach (['title', 'description', 'image', 'image_alt', 'primary_url', 'secondary_url', 'quote_title', 'badge_title'] as $field) {
            $this->assertTrue($form->has($field), $field);
        }
        foreach (range(1, 4) as $number) {
            foreach (['label', 'value', 'description', 'icon'] as $field) {
                $this->assertTrue($form->has('stat_'.$number.'_'.$field));
            }
        }
        $this->assertFalse($form->has('picture_1_image'));
    }

    public function test_intro_renders_one_image_quote_badge_and_four_information_cards(): void
    {
        $html = (string) shortcode()->compile('[BlogFengShui3][/BlogFengShui3]', true);
        $this->assertStringContainsString('Hành Trình Gìn Giữ Cổ Học', $html);
        foreach (['__copy', '__visual', '__quote', '__badge', '__stats'] as $part) {
            $this->assertStringContainsString('blog-feng-shui-3'.$part, $html);
        }
        $this->assertSame(1, substr_count($html, '<figure '));
        $this->assertSame(4, substr_count($html, 'class="blog-feng-shui-3__stat"'));
        $this->assertStringContainsString('href="/contact"', $html);
        $this->assertStringContainsString('href="/blog"', $html);
        $this->assertStringNotContainsString('Thiên Thành', $html);

        $gallery = (string) shortcode()->compile('[BlogFengShui4][/BlogFengShui4]', true);
        $this->assertStringContainsString('Trà Thiền Đàm Đạo', $gallery);
        $this->assertSame(3, substr_count($gallery, '<figure '));
        $this->assertStringNotContainsString('blog-feng-shui-3', $gallery);
    }

    public function test_custom_text_images_and_statistics_including_zero_are_preserved(): void
    {
        $html = (string) shortcode()->compile('[BlogFengShui3 title="Tiêu đề riêng" image="products/custom.png" image_alt="Ảnh riêng" primary_url="tel:0912345678" stat_1_value="25+ Năm" stat_2_value="18.000+" stat_3_value="100%" stat_4_value="0"][/BlogFengShui3]', true);
        foreach (['Tiêu đề riêng', 'products/custom.png', 'alt="Ảnh riêng"', 'href="tel:0912345678"', '25+ Năm', '18.000+', '100%', '<h3>0</h3>'] as $value) {
            $this->assertStringContainsString($value, $html);
        }
    }

    public function test_optional_copy_badge_and_buttons_can_be_hidden(): void
    {
        $html = (string) shortcode()->compile('[BlogFengShui3 eyebrow="" primary_label="" secondary_label="" quote_title="" quote_description="" badge_title="" badge_description="" image_caption="" image_eyebrow=""][/BlogFengShui3]', true);
        foreach (['class="blog-feng-shui-3__eyebrow"', 'class="blog-feng-shui-3__quote"', 'class="blog-feng-shui-3__badge"', 'class="blog-feng-shui-3__button', '<figcaption>'] as $value) {
            $this->assertStringNotContainsString($value, $html);
        }
    }

    public function test_text_is_escaped_and_unsafe_link_schemes_are_rejected(): void
    {
        $html = (string) shortcode()->compile('[BlogFengShui3 title="<script>alert(1)</script>" primary_url="javascript:alert(1)" secondary_url="data:text/html,test"][/BlogFengShui3]', true);
        $this->assertStringNotContainsString('<script>', $html);
        $this->assertStringContainsString('&lt;script&gt;', $html);
        $this->assertStringNotContainsString('href="javascript:', $html);
        $this->assertStringNotContainsString('href="data:', $html);
    }
}
