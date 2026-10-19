<?php

namespace Tests\Feature;

use Botble\Theme\Facades\Theme;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tests\TestCase;

#[RunTestsInSeparateProcesses]
#[PreserveGlobalState(false)]
class BlogFengShui4ShortcodeTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Theme::uses('martfury');
    }

    public function test_shortcode_is_registered_with_editable_gallery_and_admin_fields(): void
    {
        $registered = shortcode()->getAll();
        $this->assertArrayHasKey('BlogFengShui4', $registered);
        $form = $registered['BlogFengShui4']['admin_config']([]);

        foreach (['title', 'description', 'eyebrow', 'contact_label', 'contact_url'] as $field) {
            $this->assertTrue($form->has($field), $field);
        }
        foreach (range(1, 3) as $number) {
            foreach (['image', 'caption', 'alt'] as $field) {
                $this->assertTrue($form->has('picture_'.$number.'_'.$field));
            }
            foreach (['title', 'description', 'icon'] as $field) {
                $this->assertTrue($form->has('feature_'.$number.'_'.$field));
            }
        }
    }

    public function test_defaults_render_three_photos_and_three_features_without_old_statistics(): void
    {
        $html = (string) shortcode()->compile('[BlogFengShui4][/BlogFengShui4]', true);

        $this->assertStringContainsString('Trà Thiền Đàm Đạo', $html);
        $this->assertStringContainsString('blog-feng-shui-4__gallery', $html);
        $this->assertStringContainsString('blog-feng-shui-4__copy', $html);
        $this->assertStringContainsString('img/home-feng-shui-hero.png', $html);
        $this->assertStringContainsString('img/blog-feng-shui-compass.png', $html);
        $this->assertStringContainsString('href="/contact"', $html);
        $this->assertSame(3, substr_count($html, '<figure '));
        $this->assertSame(3, substr_count($html, 'class="blog-feng-shui-4__feature-icon"'));
        $this->assertStringNotContainsString('blog-feng-shui-4__stat', $html);
        $this->assertStringNotContainsString('Thiên Thành', $html);
        $this->assertStringNotContainsString('1900 8686', $html);
    }

    public function test_custom_images_captions_contact_and_features_are_preserved(): void
    {
        $html = (string) shortcode()->compile('[BlogFengShui4 title="Tiêu đề riêng" description="Nội dung riêng" picture_1_image="products/custom.png" picture_1_alt="Ảnh riêng" picture_1_caption="Không gian riêng" picture_2_image="products/tea.png" picture_3_image="products/library.png" contact_label="Gọi tư vấn" contact_url="tel:0912345678" feature_1_title="Điểm riêng" feature_1_description="Mô tả riêng"][/BlogFengShui4]', true);

        foreach (['Tiêu đề riêng', 'Nội dung riêng', 'products/custom.png', 'products/tea.png', 'products/library.png', 'alt="Ảnh riêng"', 'Không gian riêng', 'href="tel:0912345678"', 'Gọi tư vấn', 'Điểm riêng', 'Mô tả riêng'] as $value) {
            $this->assertStringContainsString($value, $html);
        }
    }

    public function test_optional_captions_contact_and_features_can_be_hidden(): void
    {
        $html = (string) shortcode()->compile('[BlogFengShui4 eyebrow="" contact_label="" picture_1_caption="" picture_2_caption="" picture_3_caption="" feature_1_title="" feature_1_description="" feature_2_title="" feature_2_description="" feature_3_title="" feature_3_description=""][/BlogFengShui4]', true);

        foreach (['class="blog-feng-shui-4__eyebrow"', 'class="blog-feng-shui-4__contact"', 'class="blog-feng-shui-4__features"', '<figcaption>'] as $value) {
            $this->assertStringNotContainsString($value, $html);
        }
        $this->assertSame(3, substr_count($html, '<figure '));
    }

    public function test_text_is_escaped_and_unsafe_link_schemes_are_rejected(): void
    {
        $html = (string) shortcode()->compile('[BlogFengShui4 title="<script>alert(1)</script>" description="<img/onerror=alert(1)>" picture_1_caption="<script>alert(1)</script>" contact_url="javascript:alert(1)"][/BlogFengShui4]', true);

        $this->assertStringNotContainsString('<script>', $html);
        $this->assertStringNotContainsString('<img/onerror=', $html);
        $this->assertStringContainsString('&lt;script&gt;', $html);
        $this->assertStringNotContainsString('href="javascript:', $html);
        $this->assertStringContainsString('/contact', $html);
    }
}
