<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use App\Entity\Wallet;

class WalletTest extends TestCase
{
    public function testSetBalance(): void
    {
        $wallet = new Wallet('USD');

        $wallet->setBalance(100);
        $this->assertEquals(100, $wallet->getBalance());

        $this->expectException(\Exception::class);
        $wallet->setBalance(-50);
    }

    public function testSettingInvalidPrice(): void
    {
        $this->expectException(\Error::class);
        $product = new Product('Product 1', ['USD' => -50], 'tech');
        $test = $product->getPrices();
    }

    public function testSettingInvalidMultiPrice(): void
    {
        $this->expectException(\Error::class);
        $product = new Product('Product 1', ['USD' => -50, 'EUR' => -50], 'tech');
        $test = $product->getPrices();
    }

    public function testBalanceIsInitiallyZero(): void
    {
        $wallet = new Wallet('USD');
        $this->assertEquals(0, $wallet->getBalance());
    }

    public function testAddingFundsIncreasesBalance(): void
    {
        $wallet = new Wallet('USD');
        $wallet->addFund(50);
        $this->assertEquals(50, $wallet->getBalance());
    }

    public function testRemovingFundsDecreasesBalance(): void
    {
        $wallet = new Wallet('USD');
        $wallet->addFund(50);
        $wallet->removeFund(20);
        $this->assertEquals(30, $wallet->getBalance());
    }

    public function testRemovingMoreFundsThanAvailableThrowsException(): void
    {
        $this->expectException(\Exception::class);
        $wallet = new Wallet('USD');
        $wallet->addFund(50);
        $wallet->removeFund(60);
    }

    public function testAddingNegativeFundsThrowsException(): void
    {
        $this->expectException(\Exception::class);
        $wallet = new Wallet('USD');
        $wallet->addFund(-50);
    }

    public function testRemovingNegativeFundsThrowsException(): void
    {
        $this->expectException(\Exception::class);
        $wallet = new Wallet('USD');
        $wallet->addFund(50);
        $wallet->removeFund(-20);
    }

    public function testSettingInvalidCurrencyThrowsException(): void
    {
        $this->expectException(\Exception::class);
        $wallet = new Wallet('INVALID');
    }

    public function testSettingValidCurrency(): void
    {
        $wallet = new Wallet('USD');
        $this->assertEquals('USD', $wallet->getCurrency());
    }
}