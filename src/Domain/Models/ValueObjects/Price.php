<?php

namespace Invoicing\Domain\Models\ValueObjects;

use Invoicing\Domain\Models\Item;

class Price
{
    private Money $netPrice;
    private Money $taxPrice;
    private Money $grossPrice;

    /**
     * @var Tax[]
     */
    private array $taxes = [];

    private function __construct(Money $netPrice, array $taxes = [])
    {
        $this->setNetPrice($netPrice);
        $this->addTaxes($taxes);
    }

    public static function createEmpty(Currency $currency, array $taxes = []): self
    {
        $initialNetPrice = new Money(0, $currency);

        return new self($initialNetPrice, $taxes);
    }

    /**
     * @param Item[] $items
     */
    public function withNewItems(array $items): self
    {
        $newNetPrice = new Money(0, $this->netPrice->getCurrency());

        foreach ($items as $item) {
            $newNetPrice = $newNetPrice->addMoney($item->getPrice());
        }

        return new self($newNetPrice, $this->getTaxes());
    }

    public function getNetPrice(): Money
    {
        return $this->netPrice;
    }

    private function setNetPrice(Money $netPrice)
    {
        $this->netPrice = $netPrice;

        $this->updatePrices();
    }

    public function getTaxPrice(): Money
    {
        return $this->taxPrice;
    }

    public function getGrossPrice(): Money
    {
        return $this->grossPrice;
    }

    public function getCurrency(): Currency
    {
        return $this->getNetPrice()->getCurrency();
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
    private function addTaxes(array $taxes)
    {
        foreach ($taxes as $tax) {
            $this->addTax($tax);
        }
    }

    private function addTax(Tax $tax)
    {
        $this->taxes[] = $tax;

        $this->updatePrices();
    }

    /**
     * Update Gross price and Tax price
     */
    private function updatePrices()
    {
        $this->grossPrice = clone $this->netPrice;

        if (count($this->taxes) > 0) {
            $total = 0;
            foreach ($this->taxes as $tax) {
                $total += $tax->getValue();
            }
            $this->grossPrice = $this->grossPrice->applyTax(Tax::create('Total', $total));
        }

        $this->taxPrice = $this->grossPrice->subtractMoney($this->netPrice);
    }
}
