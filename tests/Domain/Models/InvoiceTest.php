<?php

namespace Tests\Invoicing\Domain\Models;

use Invoicing\Domain\Exceptions\InvalidInvoiceStateException;
use Invoicing\Domain\Exceptions\NullAddressException;
use Invoicing\Domain\Models\Country;
use Invoicing\Domain\Models\Invoice;
use Invoicing\Domain\Models\Item;
use Invoicing\Domain\Models\Recipient;
use Invoicing\Domain\Models\ValueObjects\Address;
use Invoicing\Domain\Models\ValueObjects\Currency;
use Invoicing\Domain\Models\ValueObjects\Money;
use Invoicing\Domain\Models\ValueObjects\Tax;
use Invoicing\Domain\Models\ValueObjects\Uuid;
use PHPUnit\Framework\TestCase;

class InvoiceTest extends TestCase
{
    /**
     * @test
     */
    public function it_test_valid_invoice_without_items(): Invoice
    {
        $id = new Uuid('1ec3cc32-050c-6c5c-b6b9-0242ac140002');

        /** @var Tax[] $taxes */
        $taxes = [
            Tax::create('VAT', 0.19),
            Tax::create('Another Tax', 0.02),
        ];

        $currency = Currency::create('EGP', 'Egyptian Pound');
        $country  = Country::create('EG', 'Egypt', $currency);
        $country->addTaxes($taxes);
        $address = new Address("My Address", $country);

        $recipient = Recipient::create('1ec3cbb4-ca0a-6186-83d5-0242ac140002', 'Hazem Noor');
        $recipient->changeAddress($address);

        $invoice = new Invoice($id, $recipient);

        $this->assertEquals($id, $invoice->getId());
        $this->assertEquals($recipient->getId(), $invoice->getRecipient()->getId());
        $this->assertEquals($recipient->getAddress()->getAddress(), $invoice->getBillingAddress()->getAddress());
        $this->assertEquals(
            $recipient->getAddress()->getCountry()->getId(),
            $invoice->getBillingAddress()->getCountry()->getId()
        );
        $this->assertEmpty($invoice->getItems());

        // Price
        $expectedNetPrice   = new Money(0, $recipient->getAddress()->getCountry()->getCurrency());
        $expectedGrossPrice = clone $expectedNetPrice;
        $expectedTaxPrice   = $expectedGrossPrice->subtractMoney($expectedNetPrice);
        $this->assertEquals($expectedNetPrice, $invoice->getNetPrice());
        $this->assertEquals($expectedGrossPrice, $invoice->getGrossPrice());
        $this->assertEquals($expectedTaxPrice, $invoice->getTaxPrice());
        $this->assertEquals($recipient->getAddress()->getCountry()->getCurrency(), $invoice->getCurrency());
        $this->assertEquals($taxes, $invoice->getTaxes());

        // State
        $this->assertEquals(1, $invoice->getState());
        $this->assertTrue($invoice->isCreated());
        $this->assertFalse($invoice->isSent());
        $this->assertFalse($invoice->isPaid());

        return $invoice;
    }

    /**
     * @test
     * @depends it_test_valid_invoice_without_items
     */
    public function it_test_valid_invoice_with_factory_without_items(Invoice $invoice)
    {
        $this->assertEquals(
            $invoice->getId(),
            Invoice::create(
                $invoice->getId()->getValue(),
                $invoice->getRecipient()
            )->getId()
        );
    }

    /**
     * @test
     * @depends it_test_valid_invoice_without_items
     */
    public function it_test_valid_with_items(Invoice $anInvoice): Invoice
    {
        $uuids = [
            "1ec3c815-81a9-6e0c-b78f-0242ac140002",
            "1ec3c815-81ac-680a-bc8b-0242ac140002",
            "1ec3c815-81ac-6d0a-9c38-0242ac140002",
            "1ec3c815-81ad-6192-8ce3-0242ac140002",
            "1ec3c815-81ad-65f2-91df-0242ac140002",
            "1ec3c815-81ad-6a48-9572-0242ac140002",
            "1ec3c815-81ad-6e8a-a279-0242ac140002",
            "1ec3c815-81ae-62b8-bb3c-0242ac140002",
            "1ec3c815-81ae-66e6-a084-0242ac140002",
            "1ec3c815-81ae-6b14-bd3d-0242ac140002",
        ];

        $itemsPricesValue = 0.0;

        /** @var Item[] $items */
        $items = [];
        foreach ($uuids as $index => $uuid) {
            $itemPriceValue   = rand(100, 200);
            $itemsPricesValue += $itemPriceValue;

            $items[] = Item::create(
                $uuid,
                sprintf("Item %s", $index + 1),
                $itemPriceValue,
                $anInvoice->getCurrency()
            );
        }

        $invoice = Invoice::create($anInvoice->getId()->getValue(), $anInvoice->getRecipient(), $items);

        // Price
        $expectedNetPrice = new Money(
            $itemsPricesValue,
            $anInvoice->getRecipient()->getAddress()->getCountry()->getCurrency()
        );

        $taxesValues = 0.0;
        foreach ($anInvoice->getRecipient()->getAddress()->getCountry()->getTaxes() as $tax) {
            $taxesValues += $tax->getValue();
        }
        $taxTotal = Tax::create('Tax Total', $taxesValues);

        $expectedGrossPrice = $expectedNetPrice->applyTax($taxTotal);
        $expectedTaxPrice   = $expectedGrossPrice->subtractMoney($expectedNetPrice);

        $this->assertEquals($expectedNetPrice, $invoice->getNetPrice());
        $this->assertEquals($expectedGrossPrice, $invoice->getGrossPrice());
        $this->assertEquals($expectedTaxPrice, $invoice->getTaxPrice());
        $this->assertEquals($items, $invoice->getItems());

        return $invoice;
    }

    /**
     * @test
     * @depends it_test_valid_invoice_without_items
     */
    public function it_test_invalid_with_recipient_null_address(Invoice $anInvoice)
    {
        $recipient = clone $anInvoice->getRecipient();
        $recipient->removeAddress();

        $this->expectException(NullAddressException::class);
        Invoice::create($anInvoice->getId()->getValue(), $recipient);
    }

    /**
     * @test
     * @depends it_test_valid_invoice_without_items
     */
    public function it_test_add_items(Invoice $anInvoice)
    {
        $uuids = [
            "1ec3c815-81a9-6e0c-b78f-0242ac140002",
            "1ec3c815-81ac-680a-bc8b-0242ac140002",
            "1ec3c815-81ac-6d0a-9c38-0242ac140002",
            "1ec3c815-81ad-6192-8ce3-0242ac140002",
            "1ec3c815-81ad-65f2-91df-0242ac140002",
            "1ec3c815-81ad-6a48-9572-0242ac140002",
            "1ec3c815-81ad-6e8a-a279-0242ac140002",
            "1ec3c815-81ae-62b8-bb3c-0242ac140002",
            "1ec3c815-81ae-66e6-a084-0242ac140002",
            "1ec3c815-81ae-6b14-bd3d-0242ac140002",
        ];

        $itemsPricesValue = 0.0;

        /** @var Item[] $items */
        $items = [];
        foreach ($uuids as $index => $uuid) {
            $itemPriceValue   = rand(100, 200);
            $itemsPricesValue += $itemPriceValue;

            $items[] = Item::create(
                $uuid,
                sprintf("Item %s", $index + 1),
                $itemPriceValue,
                $anInvoice->getCurrency()
            );
        }

        $anInvoice->addItems($items);

        // Price
        $expectedNetPrice = new Money(
            $itemsPricesValue,
            $anInvoice->getRecipient()->getAddress()->getCountry()->getCurrency()
        );

        $taxesValues = 0.0;
        foreach ($anInvoice->getRecipient()->getAddress()->getCountry()->getTaxes() as $tax) {
            $taxesValues += $tax->getValue();
        }
        $taxTotal = Tax::create('Tax Total', $taxesValues);

        $expectedGrossPrice = $expectedNetPrice->applyTax($taxTotal);
        $expectedTaxPrice   = $expectedGrossPrice->subtractMoney($expectedNetPrice);

        $this->assertEquals($expectedNetPrice, $anInvoice->getNetPrice());
        $this->assertEquals($expectedGrossPrice, $anInvoice->getGrossPrice());
        $this->assertEquals($expectedTaxPrice, $anInvoice->getTaxPrice());
        $this->assertEquals($items, $anInvoice->getItems());
    }

    /**
     * @test
     * @depends it_test_valid_with_items
     */
    public function it_test_remove_item(Invoice $anInvoice)
    {
        $items = $anInvoice->getItems();
        $anInvoice->removeItem($items[0]);
        $items = array_slice($items, 1);

        $itemsPricesValue = 0.0;
        foreach ($items as $item) {
            $itemsPricesValue += $item->getPrice()->getAmount();
        }

        // Price
        $expectedNetPrice = new Money(
            $itemsPricesValue,
            $anInvoice->getRecipient()->getAddress()->getCountry()->getCurrency()
        );

        $taxesValues = 0.0;
        foreach ($anInvoice->getRecipient()->getAddress()->getCountry()->getTaxes() as $tax) {
            $taxesValues += $tax->getValue();
        }
        $taxTotal = Tax::create('Tax Total', $taxesValues);

        $expectedGrossPrice = $expectedNetPrice->applyTax($taxTotal);
        $expectedTaxPrice   = $expectedGrossPrice->subtractMoney($expectedNetPrice);

        $this->assertEquals($expectedNetPrice, $anInvoice->getNetPrice());
        $this->assertEquals($expectedGrossPrice, $anInvoice->getGrossPrice());
        $this->assertEquals($expectedTaxPrice, $anInvoice->getTaxPrice());
        $this->assertEquals($items, $anInvoice->getItems());
    }

    /**
     * @test
     * @depends it_test_valid_with_items
     */
    public function it_test_state_created_to_paid(Invoice $anInvoice)
    {
        $this->expectException(InvalidInvoiceStateException::class);
        $anInvoice->setStatePaid();
    }

    /**
     * @test
     * @depends it_test_valid_with_items
     */
    public function it_test_state_created_to_sent(Invoice $anInvoice): Invoice
    {
        $anInvoice->setStateSent();
        $this->assertTrue($anInvoice->isCreated());
        $this->assertTrue($anInvoice->isSent());
        $this->assertFalse($anInvoice->isPaid());
        $this->assertSame(3, $anInvoice->getState());

        return $anInvoice;
    }

    /**
     * @test
     * @depends it_test_state_created_to_sent
     */
    public function it_test_state_sent_to_paid(Invoice $anInvoice): Invoice
    {
        $anInvoice->setStatePaid();
        $this->assertTrue($anInvoice->isCreated());
        $this->assertTrue($anInvoice->isSent());
        $this->assertTrue($anInvoice->isPaid());
        $this->assertSame(7, $anInvoice->getState());

        return $anInvoice;
    }

    /**
     * @test
     * @depends it_test_state_sent_to_paid
     */
    public function it_test_state_paid_to_sent(Invoice $anInvoice): Invoice
    {
        $anInvoice->setStateSent();
        $this->assertTrue($anInvoice->isCreated());
        $this->assertTrue($anInvoice->isSent());
        $this->assertTrue($anInvoice->isPaid());
        $this->assertSame(7, $anInvoice->getState());

        return $anInvoice;
    }
}
