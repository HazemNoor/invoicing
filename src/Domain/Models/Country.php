<?php

namespace Invoicing\Domain\Models;

use Invoicing\Domain\Exceptions\InvalidArgumentException;
use Invoicing\Domain\Models\ValueObjects\Currency;
use Invoicing\Domain\Models\ValueObjects\Name;
use Invoicing\Domain\Models\ValueObjects\Tax;

class Country
{
    /**
     * @var string ISO 3166-1 alpha-2 country code
     */
    private string $id;

    private Name $name;

    private Currency $currency;

    /**
     * @var Tax[]
     */
    private array $taxes = [];

    public function __construct(string $id, Name $name, Currency $currency)
    {
        $this->setId($id);
        $this->name     = $name;
        $this->currency = $currency;
    }

    public static function create(string $id, string $name, Currency $currency): self
    {
        return new self($id, new Name($name), $currency);
    }

    public function getId(): string
    {
        return $this->id;
    }

    private function setId(string $id)
    {
        $id = strtoupper($id);
        if (!preg_match('/^[A-Z]{2}$/', $id)) {
            throw InvalidArgumentException::create('id', $id, 'not a valid ISO 3166-1 alpha-2 country code.');
        }
        $this->id = $id;
    }

    public function getName(): Name
    {
        return $this->name;
    }

    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    /**
     * @return Tax[]
     */
    public function getTaxes(): array
    {
        return $this->taxes;
    }

    /**
     * @param Tax[] $taxes
     */
    public function addTaxes(array $taxes)
    {
        foreach ($taxes as $tax) {
            $this->addTax($tax);
        }
    }

    public function addTax(Tax $tax)
    {
        $this->taxes[] = $tax;
    }

    public function removeTaxes()
    {
        $this->taxes = [];
    }
}
