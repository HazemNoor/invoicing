<?php

namespace Invoicing\Domain\Models\ValueObjects;

use Invoicing\Domain\Exceptions\InvalidArgumentException;

class Currency
{
    /**
     * @var string ISO 4217 currency code
     */
    private string $code;

    private Name $name;

    public function __construct(string $code, Name $name)
    {
        $this->setCode($code);
        $this->name = $name;
    }

    public static function create(string $code, string $name): self
    {
        return new self($code, new Name($name));
    }

    public function getCode(): string
    {
        return $this->code;
    }

    private function setCode(string $code)
    {
        $code = strtoupper($code);
        if (!preg_match('/^[A-Z]{3}$/', $code)) {
            throw InvalidArgumentException::create('code', $code, 'not a valid ISO 4217 currency code.');
        }
        $this->code = $code;
    }

    public function getName(): Name
    {
        return $this->name;
    }
}
