<?php

namespace Tests\Invoicing\Domain\Models\ValueObjects;

use Invoicing\Domain\Exceptions\InvalidArgumentException;
use Invoicing\Domain\Models\ValueObjects\Currency;
use Invoicing\Domain\Models\ValueObjects\Money;
use Invoicing\Domain\Models\ValueObjects\Tax;
use PHPUnit\Framework\TestCase;

class MoneyTest extends TestCase
{
    /**
     * @test
     */
    public function it_test_valid_money(): Money
    {
        $amount   = 3500;
        $currency = Currency::create('EUR', 'Euro');
        $money    = new Money($amount, $currency);

        $this->assertEquals($amount, $money->getAmount());
        $this->assertEquals($currency, $money->getCurrency());

        return $money;
    }

    /**
     * @test
     */
    public function it_test_valid_money_zero_amount()
    {
        $currency = Currency::create('EUR', 'Euro');
        $money    = new Money(0, $currency);

        $this->assertEquals(0, $money->getAmount());
    }

    /**
     * @test
     * @depends it_test_valid_money
     */
    public function it_test_change_amount_of_same_currency(Money $money)
    {
        $newAmount = 50;
        $newMoney  = $money->changeAmount($newAmount);

        $this->assertEquals($newAmount, $newMoney->getAmount());
        $this->assertEquals($money->getCurrency(), $newMoney->getCurrency());

        // The two objects are equal but not the same
        $newMoney = $money->changeAmount($money->getAmount());
        $this->assertEquals($newMoney, $money);
        $this->assertNotSame($newMoney, $money);
    }

    /**
     * @test
     */
    public function it_test_invalid_negative_value()
    {
        $this->expectException(InvalidArgumentException::class);
        new Money(-3500, Currency::create('EUR', 'Euro'));
    }

    /**
     * @test
     * @depends it_test_valid_money
     */
    public function it_test_add_same_currency(Money $money)
    {
        $amount = 15;

        $newMoney = $money->addMoney(new Money($amount, $money->getCurrency()));

        $this->assertEquals($money->getAmount() + $amount, $newMoney->getAmount());
        $this->assertEquals($money->getCurrency(), $newMoney->getCurrency());

        // The two objects are equal but not the same
        $newMoney = $money->addMoney(new Money(0, $money->getCurrency()));
        $this->assertEquals($newMoney, $money);
        $this->assertNotSame($newMoney, $money);
    }

    /**
     * @test
     * @depends it_test_valid_money
     */
    public function it_test_add_different_currency(Money $money)
    {
        $this->expectException(\InvalidArgumentException::class);
        $money->addMoney(new Money(15, Currency::create('EGT', 'Egyptian Pound')));
    }

    /**
     * @test
     * @depends it_test_valid_money
     */
    public function it_test_subtract_same_currency(Money $money)
    {
        // subtract small value
        $amount   = 15;
        $newMoney = $money->subtractMoney(new Money($amount, $money->getCurrency()));

        $this->assertEquals($money->getAmount() - $amount, $newMoney->getAmount());
        $this->assertEquals($money->getCurrency(), $newMoney->getCurrency());

        // The two objects are equal but not the same
        $newMoney = $money->subtractMoney(new Money(0, $money->getCurrency()));
        $this->assertEquals($newMoney, $money);
        $this->assertNotSame($newMoney, $money);

        // Subtract big value yields to result negative value
        $amount = $money->getAmount() + 1;
        $this->expectException(InvalidArgumentException::class);
        $money->subtractMoney(new Money($amount, $money->getCurrency()));
    }

    /**
     * @test
     * @depends it_test_valid_money
     */
    public function it_test_subtract_different_currency(Money $money)
    {
        $this->expectException(\InvalidArgumentException::class);
        $money->subtractMoney(new Money(15, Currency::create('EGT', 'Egyptian Pound')));
    }

    /**
     * @test
     * @depends it_test_valid_money
     */
    public function it_test_apply_tax(Money $money)
    {
        $tax = Tax::create('VAT', 0.19);

        $newMoney = $money->applyTax($tax);

        $this->assertEquals($money->getAmount() + ($money->getAmount() * $tax->getValue()), $newMoney->getAmount());
        $this->assertEquals($money->getCurrency(), $newMoney->getCurrency());

        // If Zero tax applied, must return a new object
        $tax      = Tax::create('VAT', 0);
        $newMoney = $money->applyTax($tax);
        $this->assertEquals($newMoney, $money);
        $this->assertNotSame($newMoney, $money);
    }
}
