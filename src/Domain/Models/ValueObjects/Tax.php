<?php

namespace Invoicing\Domain\Models\ValueObjects;

use Invoicing\Domain\Exceptions\InvalidArgumentException;

class Tax
{
    private Name $name;
    private float $value;

    public function __construct(Name $name, float $value)
    {
        $this->name = $name;
        $this->setValue($value);
    }

    public static function create(string $name, float $value): self
    {
        return new self(new Name($name), $value);
    }

    public function getName(): Name
    {
        return $this->name;
    }

    public function getValue(): float
    {
        return $this->value;
    }

    private function setValue(float $value)
    {
        if ($value < 0 || $value > 1) {
            throw InvalidArgumentException::create(
                'value',
                $value,
                'tax value must be between 0 and 1.'
            );
        }

        $this->value = $value;
    }
}
