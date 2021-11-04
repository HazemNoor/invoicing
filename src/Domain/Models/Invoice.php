<?php

namespace Invoicing\Domain\Models;

use Invoicing\Domain\Exceptions\InvalidInvoiceStateException;
use Invoicing\Domain\Exceptions\NullAddressException;
use Invoicing\Domain\Models\ValueObjects\Address;
use Invoicing\Domain\Models\ValueObjects\Currency;
use Invoicing\Domain\Models\ValueObjects\Money;
use Invoicing\Domain\Models\ValueObjects\Price;
use Invoicing\Domain\Models\ValueObjects\Tax;
use Invoicing\Domain\Models\ValueObjects\Uuid;

class Invoice
{
    private const STATE_NONE    = 0; // 000 binary
    private const STATE_CREATED = 1; // 001 binary
    private const STATE_SENT    = 2; // 010 binary
    private const STATE_PAID    = 4; // 100 binary

    /**
     * @var Uuid To have an id that can be ordered, we should use Uuid version 6
     */
    private Uuid $id;

    private Recipient $recipient;

    private Address $billingAddress;

    /**
     * @var Item[]
     */
    private array $items = [];

    private Price $price;

    private int $state = self::STATE_NONE;

    /** @todo To be implemented */
    // private \DateTimeInterface $createdAt;

    /** @todo To be implemented */
    // private \DateTimeInterface $updatedAt;

    /**
     * @param Uuid      $id
     * @param Recipient $recipient
     * @param Item[]    $items
     */
    public function __construct(Uuid $id, Recipient $recipient, array $items = [])
    {
        $this->id = $id;
        $this->setRecipient($recipient);
        $this->billingAddress = $recipient->getAddress();
        $this->setPrice(
            $recipient->getAddress()->getCountry()->getCurrency(),
            $recipient->getAddress()->getCountry()->getTaxes()
        );
        $this->addItems($items);

        $this->setStateCreated();
    }

    public static function create(string $id, Recipient $recipient, array $items = []): self
    {
        return new self(new Uuid($id), $recipient, $items);
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getRecipient(): Recipient
    {
        return $this->recipient;
    }

    private function setRecipient(Recipient $recipient)
    {
        if (is_null($recipient->getAddress())) {
            throw new NullAddressException("Recipient doesn't have an address yet.");
        }
        $this->recipient = $recipient;
    }

    public function getBillingAddress(): Address
    {
        return $this->billingAddress;
    }

    public function getCurrency(): Currency
    {
        return $this->getPrice()->getCurrency();
    }

    public function getNetPrice(): Money
    {
        return $this->getPrice()->getNetPrice();
    }

    public function getGrossPrice(): Money
    {
        return $this->getPrice()->getGrossPrice();
    }

    public function getTaxPrice(): Money
    {
        return $this->getPrice()->getTaxPrice();
    }

    /**
     * @return Tax[]
     */
    public function getTaxes(): array
    {
        return $this->getPrice()->getTaxes();
    }

    private function setPrice(Currency $currency, array $taxes)
    {
        $this->price = Price::createEmpty($currency, $taxes);
    }

    /**
     * @return Item[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @param Item[] $items
     */
    public function addItems(array $items)
    {
        foreach ($items as $item) {
            $this->addItem($item);
        }
    }

    public function addItem(Item $item)
    {
        // todo: we may restrict updating an invoice in case of sent or paid
        $this->items[] = $item;

        $this->price = $this->price->withNewItems($this->items);
    }

    public function removeItem(Item $item)
    {
        // todo: we may restrict updating an invoice in case of sent or paid
        $index = $this->getItemIndex($item);
        if (!is_null($index)) {
            unset($this->items[$index]);
        }
        $this->items = array_values($this->items); // Reset indices

        $this->price = $this->price->withNewItems($this->items);
    }

    /**
     * Search for an item in the current items list, returning its index
     */
    private function getItemIndex(Item $item): ?int
    {
        foreach ($this->items as $anIndex => $anItem) {
            if ($anItem->getId() === $item->getId()) {
                return $anIndex;
            }
        }

        return null;
    }

    public function getPrice(): Price
    {
        return $this->price;
    }

    public function getState(): int
    {
        return $this->state;
    }

    public function isCreated(): bool
    {
        return boolval($this->state & self::STATE_CREATED);
    }

    public function isSent(): bool
    {
        return boolval($this->state & self::STATE_SENT);
    }

    public function isPaid(): bool
    {
        return boolval($this->state & self::STATE_PAID);
    }

    private function setStateCreated()
    {
        $this->addState(self::STATE_CREATED);
    }

    public function setStateSent()
    {
        $this->addState(self::STATE_SENT);
    }

    public function setStatePaid()
    {
        if (!$this->isSent()) {
            throw new InvalidInvoiceStateException("Invoice can't be paid before being sent.");
        }

        $this->addState(self::STATE_PAID);
    }

    private function addState(int $state)
    {
        $this->state |= $state;
    }
}
