<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use App\Entity\Product;

class ProductTest extends TestCase
{
    public function testSetPrices(): void
    {
        $product = new Product('Test Product', [], 'food');

        $product->setPrices(['USD' => 100, 'EUR' => 90]);
        $this->assertEquals(['USD' => 100, 'EUR' => 90], $product->getPrices());

        $product = new Product('Test Product', ['USD' => 100], 'food');
        $product->setPrices(['USD' => -50, 'EUR' => 90]);
        $this->assertEquals(['USD'=>100, 'EUR' => 90], $product->getPrices());

        $product = new Product('Test Product', ['USD' => 100], 'food');
        $product->setPrices(['USD' => 100, 'JPY' => 10000]);
        $this->assertEquals(['USD' => 100], $product->getPrices());
    }

    public function testExecptionGetPrice(): void
    {
        $this->expectException(\Exception::class);
        $product = new Product('Test Product', ['USD' => 100], 'food');
        $product->getPrice('EUR');
    }
    public function testPricesInDifferentCurrencies(): void
    {
        $product = new Product('Product 1', ['USD' => 50, 'EUR' => 45], 'tech');
        $this->assertEquals(50, $product->getPrice('USD'));
        $this->assertEquals(45, $product->getPrice('EUR'));
    }

    public function testInvalidCurrencyThrowsException(): void
    {
        $this->expectException(\Exception::class);
        $product = new Product('Product 1', ['USD' => 50], 'tech');
        $product->getPrice('GBP');
    }

    public function testSettingInvalidTypeThrowsException(): void
    {
        $this->expectException(\Exception::class);
        $product = new Product('Product 1', ['USD' => 50], 'invalidType');
    }

    public function testSettingValidType(): void
    {
        $product = new Product('Product 1', ['USD' => 50], 'tech');
        $this->assertEquals('tech', $product->getType());
    }

    public function testSettingValidPrice(): void
    {
        $product = new Product('Product 1', ['USD' => 50], 'tech');
        $this->assertEquals(['USD' => 50], $product->getPrices());
    }

    public function testGetTVAForFoodProduct(): void
    {
        $product = new Product('Product 1', ['USD' => 50], 'food');
        $this->assertEquals(0.1, $product->getTVA());
    }

    public function testGetTVAForNonFoodProduct(): void
    {
        $product = new Product('Product 1', ['USD' => 50], 'tech');
        $this->assertEquals(0.2, $product->getTVA());
    }
}