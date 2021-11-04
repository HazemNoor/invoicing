<?php

namespace Tests\Invoicing\Domain\Models\ValueObjects;

use Invoicing\Domain\Exceptions\InvalidArgumentException;
use Invoicing\Domain\Models\Country;
use Invoicing\Domain\Models\ValueObjects\Address;
use Invoicing\Domain\Models\ValueObjects\Currency;
use PHPUnit\Framework\TestCase;

class AddressTest extends TestCase
{
    /**
     * @test
     */
    public function it_test_valid_address(): Address
    {
        $eur     = Currency::create('EUR', 'Euro');
        $germany = Country::create('DE', 'Germany', $eur);

        $addressText = "My street name";
        $address     = new Address($addressText, $germany);

        $this->assertEquals($addressText, $address->getAddress());
        $this->assertEquals($germany, $address->getCountry());

        return $address;
    }

    /**
     * @test
     */
    public function it_test_empty_address()
    {
        $eur     = Currency::create('EUR', 'Euro');
        $germany = Country::create('DE', 'Germany', $eur);

        $this->expectException(InvalidArgumentException::class);
        new Address('', $germany);
    }

    /**
     * @test
     * @depends it_test_valid_address
     */
    public function it_test_change_address(Address $address)
    {
        $newAddressText = 'Another street name';
        $newAddress     = $address->changeAddress($newAddressText);

        $this->assertEquals($newAddressText, $newAddress->getAddress());

        // The two objects are equal but not the same
        $newAddress = $address->changeAddress($address->getAddress());
        $this->assertEquals($newAddress, $address);
        $this->assertNotSame($newAddress, $address);
    }

    /**
     * @test
     * @depends it_test_valid_address
     */
    public function it_test_change_country(Address $address)
    {
        $egp   = Currency::create('EGP', 'Egyptian Pound');
        $egypt = Country::create('EG', 'Egypt', $egp);

        $newAddress = $address->changeCountry($egypt);

        $this->assertEquals($egypt, $newAddress->getCountry());

        // The two objects are equal but not the same
        $newAddress = $address->changeCountry($address->getCountry());
        $this->assertEquals($newAddress, $address);
        $this->assertNotSame($newAddress, $address);
    }
}
