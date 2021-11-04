<?php

namespace Tests\Invoicing\Domain\Models\ValueObjects;

use Invoicing\Domain\Exceptions\InvalidArgumentException;
use Invoicing\Domain\Models\ValueObjects\Currency;
use Invoicing\Domain\Models\ValueObjects\Name;
use PHPUnit\Framework\TestCase;

class CurrencyTest extends TestCase
{
    /**
     * @test
     */
    public function it_test_valid_currency(): Currency
    {
        $code = 'EGP';
        $name = 'Egyptian Pound';

        $currency = new Currency($code, new Name($name));
        $this->assertEquals($code, $currency->getCode());
        $this->assertEquals($name, $currency->getName()->getValue());

        // Check factory method
        $currencyWithFactory = Currency::create($code, $name);
        $this->assertEquals($currency, $currencyWithFactory);

        return $currency;
    }

    /**
     * @test
     * @depends it_test_valid_currency
     */
    public function it_test_valid_currency_with_factory(Currency $currency)
    {
        $currencyWithFactory = Currency::create($currency->getCode(), $currency->getName()->getValue());

        $this->assertEquals($currency, $currencyWithFactory);
    }

    /**
     * @test
     */
    public function it_test_invalid_code()
    {
        $this->expectException(InvalidArgumentException::class);
        new Currency('invalid-code', new Name('Egyptian Pound'));
    }

    /**
     * @test
     */
    public function it_test_invalid_empty_code()
    {
        $this->expectException(InvalidArgumentException::class);
        new Currency('', new Name('Egyptian Pound'));
    }
}
