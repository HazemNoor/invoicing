<?php

namespace Invoicing\Domain\Models;

use Invoicing\Domain\Models\ValueObjects\Currency;
use Invoicing\Domain\Models\ValueObjects\Money;
use Invoicing\Domain\Models\ValueObjects\Name;
use Invoicing\Domain\Models\ValueObjects\Uuid;

class Item
{
    private Uuid $id;

    private Name $name;

    private Money $price;

    public function __construct(Uuid $id, Name $name, Money $price)
    {
        $this->id    = $id;
        $this->name  = $name;
        $this->price = $price;
    }

    public static function create(string $id, string $name, float $priceAmount, Currency $priceCurrency): self
    {
        return new self(new Uuid($id), new Name($name), new Money($priceAmount, $priceCurrency));
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getName(): Name
    {
        return $this->name;
    }

    public function changeName(string $name)
    {
        $this->name = new Name($name);
    }

    public function getPrice(): Money
    {
        return $this->price;
    }

    public function changePrice(Money $price)
    {
        $this->price = $price;
    }
}
