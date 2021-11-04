<?php

namespace Invoicing\Domain\Models\ValueObjects;

use Invoicing\Domain\Exceptions\InvalidArgumentException;

class Money
{
    private float $amount;

    private Currency $currency;

    public function __construct(float $amount, Currency $currency)
    {
        $this->setAmount($amount);
        $this->currency = $currency;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    private function setAmount(float $amount)
    {
        if ($amount < 0) {
            throw InvalidArgumentException::create('amount', $amount, 'amount cannot be a negative value.');
        }

        $this->amount = $amount;
    }

    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    public function changeAmount(float $newAmount): self
    {
        return new self($newAmount, $this->getCurrency());
    }

    private function checkSameCurrency(Money $money)
    {
        if ($this->getCurrency()->getCode() !== $money->getCurrency()->getCode()) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Money must be of the same Currency ["%s" !== "%s"].',
                    $this->getCurrency()->getCode(),
                    $money->getCurrency()->getCode()
                )
            );
        }
    }

    public function addMoney(Money $money): self
    {
        $this->checkSameCurrency($money);

        return new self(
            $this->getAmount() + $money->getAmount(),
            $this->getCurrency()
        );
    }

    public function subtractMoney(Money $money): self
    {
        $this->checkSameCurrency($money);

        return new self(
            $this->getAmount() - $money->getAmount(),
            $this->getCurrency()
        );
    }

    /**
     * Add a new amount from applying a certain Tax
     */
    public function applyTax(Tax $tax): self
    {
        return new self(
            $this->getAmount() + ($this->getAmount() * $tax->getValue()),
            $this->getCurrency()
        );
    }
}
