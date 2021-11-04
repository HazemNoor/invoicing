<?php

namespace Invoicing\Domain\Models\ValueObjects;

use Invoicing\Domain\Exceptions\InvalidArgumentException;
use Invoicing\Domain\Models\Country;

class Address
{
    private string $address;

    private Country $country;

    public function __construct(string $address, Country $country)
    {
        $this->setAddress($address);
        $this->country = $country;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    private function setAddress(string $address)
    {
        if (empty($address)) {
            throw InvalidArgumentException::create('address', $address, 'address cannot be empty.');
        }

        $this->address = $address;
    }

    public function getCountry(): Country
    {
        return $this->country;
    }

    public function changeAddress(string $address): self
    {
        return new self($address, $this->getCountry());
    }

    public function changeCountry(Country $country): self
    {
        return new self($this->getAddress(), $country);
    }
}
