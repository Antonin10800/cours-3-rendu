<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use App\Entity\Wallet;
use App\Entity\Product;
use App\Entity\Person;

class ExempleTest extends TestCase
{
    public function testSetBalance(): void
    {
        $wallet = new Wallet('USD');

        $wallet->setBalance(100);
        $this->assertEquals(100, $wallet->getBalance());

        $this->expectException(\Exception::class);
        $wallet->setBalance(-50);
    }

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

    public function testGetName(): void
    {
        $product = new Product('Test Product', ['USD' => 100], 'food');
        $this->assertEquals('Test Product', $product->getName());
    }

    public function testBuyProduct(): void
    {
        $wallet = new Wallet('USD');
        $wallet->setBalance(100);
        $person = new Person('Test Person', $wallet->getCurrency());
        $person->setWallet($wallet);

        $product = new Product('Test Product', ['USD' => 50], 'food');
        $person->buyProduct($product);

        $this->expectException(\Exception::class);
        $product = new Product('Test Product', ['EUR' => 50], 'food');
        $person->buyProduct($product);

        $this->expectException(\Exception::class);
        $product = new Product('Test Product', ['USD' => 200], 'food');
        $person->buyProduct($product);
    }

    public function testSetNameAndGetTheName(): void
    {
        $person = new Person('John Doe', 'USD');
        $person->setName('Jane Doe');
        $this->assertEquals('Jane Doe', $person->getName());
    }

    public function testHasFundReturnsTrueWhenWalletHasBalance(): void
    {
        $wallet = new Wallet('USD');
        $wallet->addFund(100);
        $person = new Person('John Doe', 'USD');
        $person->setWallet($wallet);
        $this->assertTrue($person->hasFund());
    }

    public function testHasFundReturnsFalseWhenWalletHasNoBalance(): void
    {
        $person = new Person('John Doe', 'USD');
        $this->assertTrue($person->hasFund());
    }

    public function testTransfertFundThrowsExceptionWhenCurrenciesAreDifferent(): void
    {
        $this->expectException(\Exception::class);
        $person1 = new Person('John Doe', 'USD');
        $person2 = new Person('Jane Doe', 'EUR');
        $person1->transfertFund(100, $person2);
    }

    public function testTransfertFundTransfersFundsBetweenPersons(): void
    {
        $person1 = new Person('John Doe', 'USD');
        $person1->getWallet()->addFund(100);
        $person2 = new Person('Jane Doe', 'USD');
        $person1->transfertFund(50, $person2);
        $this->assertEquals(50, $person1->getWallet()->getBalance());
        $this->assertEquals(50, $person2->getWallet()->getBalance());
    }

    public function testDivideWalletDividesFundsAmongPersons(): void
    {
        $person1 = new Person('John Doe', 'USD');
        $person1->getWallet()->addFund(100);
        $person2 = new Person('Jane Doe', 'USD');
        $person3 = new Person('Jim Doe', 'USD');
        $person1->divideWallet([$person2, $person3]);
        $this->assertEquals(0, $person1->getWallet()->getBalance());
        $this->assertEquals(50, $person2->getWallet()->getBalance());
        $this->assertEquals(50, $person3->getWallet()->getBalance());
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

    public function testBuyProductThrowsExceptionWhenCurrenciesAreDifferent(): void
    {
        $this->expectException(\Exception::class);
        $person = new Person('John Doe', 'USD');
        $product = new Product('Product 1', ['EUR' => 100], "FOOD_PRODUCT");
        $person->buyProduct($product);
    }

    public function testBuyProductDecreasesWalletBalance(): void
    {
        $person = new Person('John Doe', 'USD');
        $person->getWallet()->addFund(100);
        $product = new Product('Product 1', ['USD' => 50], "food");
        $person->buyProduct($product);
        $this->assertEquals(50, $person->getWallet()->getBalance());
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