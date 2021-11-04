<?php

namespace Invoicing\Domain\Models\ValueObjects;

use Invoicing\Domain\Exceptions\InvalidArgumentException;

class Uuid
{
    /**
     * @var string RFC 4122, Universally Unique Identifier
     */
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
        if (!preg_match('{^[0-9a-f]{8}(?:-[0-9a-f]{4}){3}-[0-9a-f]{12}$}Di', $value)) {
            throw InvalidArgumentException::create('value', $value, 'not a valid Uuid.');
        }

        $this->value = $value;
    }
}
