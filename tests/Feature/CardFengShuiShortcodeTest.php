<?php

namespace Tests\Feature;

use Botble\Theme\Facades\Theme;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tests\TestCase;

#[RunTestsInSeparateProcesses]
#[PreserveGlobalState(false)]
class CardFengShuiShortcodeTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Theme::uses('martfury');
    }

    public function test_shortcode_is_registered_with_editable_header_and_three_cards(): void
    {
        $registered = shortcode()->getAll();
        $this->assertArrayHasKey('CardFengShui', $registered);
        $form = $registered['CardFengShui']['admin_config']([]);
        foreach (['eyebrow', 'title', 'subtitle'] as $field) {
            $this->assertTrue($form->has($field));
        }
        foreach (range(1, 3) as $number) {
            foreach (['icon', 'title', 'description', 'note'] as $field) {
                $this->assertTrue($form->has('card_'.$number.'_'.$field));
            }
        }
    }

    public function test_defaults_render_three_cards_with_icons_and_notes(): void
    {
        $html = (string) shortcode()->compile('[CardFengShui][/CardFengShui]', true);
        $this->assertStringContainsString('Ba Trụ Cột Vững Bền', $html);
        $this->assertStringContainsString('Minh Bạch Học Thuật', $html);
        $this->assertStringContainsString('Dĩ Tâm Vi Bản', $html);
        $this->assertSame(3, substr_count($html, 'class="card-feng-shui__card"'));
        $this->assertSame(3, substr_count($html, 'class="card-feng-shui__icon"'));
        $this->assertSame(3, substr_count($html, 'class="card-feng-shui__note"'));
        $this->assertStringNotContainsString('mã hóa độc quyền', $html);
    }

    public function test_custom_card_content_and_zero_values_are_preserved(): void
    {
        $html = (string) shortcode()->compile('[CardFengShui title="Tiêu đề riêng" card_1_title="Thẻ riêng" card_1_description="Mô tả riêng" card_1_note="0"][/CardFengShui]', true);
        foreach (['Tiêu đề riêng', 'Thẻ riêng', 'Mô tả riêng', '<span>0</span>'] as $value) {
            $this->assertStringContainsString($value, $html);
        }
    }

    public function test_optional_labels_can_be_hidden_and_html_is_escaped(): void
    {
        $html = (string) shortcode()->compile('[CardFengShui eyebrow="" subtitle="" card_1_icon="" card_1_note="" card_2_note="" card_3_note="" card_1_title="<script>alert(1)</script>"][/CardFengShui]', true);
        $this->assertStringNotContainsString('class="card-feng-shui__eyebrow"', $html);
        $this->assertStringNotContainsString('class="card-feng-shui__subtitle"', $html);
        $this->assertStringNotContainsString('class="card-feng-shui__note"', $html);
        $this->assertSame(2, substr_count($html, 'class="card-feng-shui__icon"'));
        $this->assertStringNotContainsString('<script>', $html);
        $this->assertStringContainsString('&lt;script&gt;', $html);
    }
}
