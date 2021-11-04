<?php

namespace Tests\Invoicing\Domain\Models;

use Invoicing\Domain\Exceptions\InvalidArgumentException;
use Invoicing\Domain\Models\Country;
use Invoicing\Domain\Models\ValueObjects\Currency;
use Invoicing\Domain\Models\ValueObjects\Name;
use Invoicing\Domain\Models\ValueObjects\Tax;
use PHPUnit\Framework\TestCase;

class CountryTest extends TestCase
{
    /**
     * @test
     */
    public function it_test_valid_country(): Country
    {
        $id = 'DE';

        $name     = new Name('Germany');
        $currency = Currency::create('EUR', 'Euro');

        $country = new Country($id, $name, $currency);

        $this->assertEquals($id, $country->getId());
        $this->assertEquals($name, $country->getName());
        $this->assertEquals($currency, $country->getCurrency());
        $this->assertEmpty($country->getTaxes());

        return $country;
    }

    /**
     * @test
     * @depends it_test_valid_country
     */
    public function it_test_valid_country_with_factory(Country $country)
    {
        $countryWithFactory = Country::create(
            $country->getId(),
            $country->getName()->getValue(),
            $country->getCurrency()
        );

        $this->assertEquals($country->getId(), $countryWithFactory->getId());
        $this->assertEquals($country->getName(), $countryWithFactory->getName());
        $this->assertEquals($country->getCurrency(), $countryWithFactory->getCurrency());
        $this->assertEquals($country->getTaxes(), $countryWithFactory->getTaxes());
    }

    /**
     * @test
     */
    public function it_test_invalid_id()
    {
        $this->expectException(InvalidArgumentException::class);
        Country::create('invalid-id', 'Germany', Currency::create('EUR', 'Euro'));
    }

    /**
     * @test
     * @depends it_test_valid_country
     */
    public function it_test_add_taxes(Country $country)
    {
        $oldTaxes = $country->getTaxes();

        /** @var Tax[] $taxes */
        $newTaxes = [
            Tax::create('VAT', 0.19),
            Tax::create('Another Tax', 0.02),
        ];
        $country->addTaxes($newTaxes);

        $this->assertNotEquals($oldTaxes, $country->getTaxes());
        $this->assertEquals($newTaxes, $country->getTaxes());
    }

    /**
     * @test
     * @depends it_test_valid_country
     */
    public function it_test_remove_taxes(Country $country)
    {
        $country->removeTaxes();

        $this->assertempty($country->getTaxes());
    }
}
