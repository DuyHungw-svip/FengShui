<?php

namespace Tests\Feature;

use Botble\Theme\Facades\Theme;
use DOMDocument;
use DOMXPath;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tests\TestCase;

#[RunTestsInSeparateProcesses]
#[PreserveGlobalState(false)]
class FengShuiHeaderTest extends TestCase
{
    public function test_home_navigation_hides_requested_shop_controls_and_keeps_other_controls(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $xpath = $this->xpath($response->getContent());

        foreach ([
            '//header//*[contains(@class, "menu--product-categories")]',
            '//header//*[contains(@class, "btn-shopping-cart")]',
            '//header//*[contains(@class, "sell-on-marketplace-link")]',
            '//header//a[contains(@href, "/currency/switch/")]',
            '//header//a[contains(@href, "/orders/tracking")]',
            '//*[@id="menu-mobile"]//a[contains(@href, "/currency/switch/")]',
            '//*[@id="menu-mobile"]//a[contains(@href, "/orders/tracking")]',
            '//a[@href="#cart-mobile" or @href="#navigation-mobile"]',
        ] as $selector) {
            $this->assertSame(0, $xpath->query($selector)->length, $selector);
        }

        $this->assertGreaterThan(0, $xpath->query('//header//a[contains(@class, "ps-logo")]/img')->length);
        $this->assertGreaterThan(0, $xpath->query('//header//form[contains(@class, "ps-form--quick-search")]')->length);
        $this->assertGreaterThan(0, $xpath->query('//header//*[contains(@class, "ps-block--user-header")]')->length);
        $this->assertGreaterThan(0, $xpath->query('//header//nav//ul[contains(@class, "menu")]/li/a')->length);
        $this->assertSame(1, $xpath->query('//a[@href="#menu-mobile"]')->length);
        $this->assertSame(1, $xpath->query('//a[@href="#search-sidebar"]')->length);
    }

    public function test_product_mobile_header_keeps_account_and_back_link_without_cart_icon(): void
    {
        Theme::uses('martfury');
        $xpath = $this->xpath(Theme::partial('header-mobile-product'));

        $this->assertSame(0, $xpath->query('//*[contains(@class, "btn-shopping-cart")]')->length);
        $this->assertSame(1, $xpath->query('//a[contains(@class, "header__back")]')->length);
        $this->assertSame(1, $xpath->query('//*[contains(@class, "ps-block--user-header")]')->length);
    }

    private function xpath(string $html): DOMXPath
    {
        $document = new DOMDocument();
        $previous = libxml_use_internal_errors(true);
        $document->loadHTML('<?xml encoding="utf-8" ?>' . $html);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        return new DOMXPath($document);
    }
}
