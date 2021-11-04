<?php

namespace Invoicing\Domain\Models;

use Invoicing\Domain\Models\ValueObjects\Address;
use Invoicing\Domain\Models\ValueObjects\Name;
use Invoicing\Domain\Models\ValueObjects\Uuid;

class Recipient
{
    private Uuid $id;
    private Name $name;
    private ?Address $address = null;

    public function __construct(Uuid $id, Name $name)
    {
        $this->id   = $id;
        $this->name = $name;
    }

    public static function create(string $id, string $name): self
    {
        return new self(new Uuid($id), new Name($name));
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

    public function getAddress(): ?Address
    {
        return $this->address;
    }

    public function changeAddress(Address $address)
    {
        $this->address = $address;
    }

    public function removeAddress()
    {
        $this->address = null;
    }
}
