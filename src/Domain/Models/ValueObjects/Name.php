<?php

namespace Invoicing\Domain\Models\ValueObjects;

use Invoicing\Domain\Exceptions\InvalidArgumentException;

/**
 * A non-empty Name
 */
class Name
{
    private string $value;

    public function __construct(string $value)
    {
        $this->setValue($value);
    }

    public function getValue(): string
    {
        return $this->value;
    }

    private function setValue(string $value)
    {
        if (empty($value)) {
            throw InvalidArgumentException::create('value', $value, 'name cannot be empty.');
        }

        $this->value = $value;
    }
}
