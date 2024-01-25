<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use App\Entity\Person;
use App\Entity\Wallet;
use App\Entity\Product;

class PersonTest extends TestCase
{
    public function testSetNameAndGetTheName(): void
    {
        $person = new Person('John Doe', 'USD');
        $person->setName('Jane Doe');
        $this->assertEquals('Jane Doe', $person->getName());
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

    public function testHasFundReturnsTrueWhenWalletHasBalance(): void
    {
        $wallet = new Wallet('USD');
        $wallet->addFund(100);
        $person = new Person('John Doe', 'USD');
        $person->setWallet($wallet);
        $this->assertTrue($person->hasFund());
    }
}