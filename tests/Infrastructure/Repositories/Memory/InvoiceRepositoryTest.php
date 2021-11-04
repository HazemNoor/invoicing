<?php

namespace Tests\Invoicing\Infrastructure\Repositories\Memory;

use Invoicing\Domain\Models\Country;
use Invoicing\Domain\Models\Invoice;
use Invoicing\Domain\Models\Recipient;
use Invoicing\Domain\Models\ValueObjects\Address;
use Invoicing\Domain\Models\ValueObjects\Currency;
use Invoicing\Domain\Models\ValueObjects\Tax;
use Invoicing\Domain\Models\ValueObjects\Uuid;
use Invoicing\Domain\Repositories\InvoiceRepository as InvoiceRepositoryInterface;
use Invoicing\Infrastructure\Repositories\Memory\InvoiceRepository;
use PHPUnit\Framework\TestCase;

class InvoiceRepositoryTest extends TestCase
{
    /**
     * @test
     */
    public function it_test_save_invoices(): InvoiceRepositoryInterface
    {
        $id = new Uuid('1ec3d3cf-9916-623c-a553-0242ac140002');

        /** @var Tax[] $taxes */
        $taxes = [
            Tax::create('VAT', 0.19),
            Tax::create('Another Tax', 0.02),
        ];

        $currency = Currency::create('EGP', 'Egyptian Pound');
        $country  = Country::create('EG', 'Egypt', $currency);
        $country->addTaxes($taxes);
        $address1 = new Address("My Address 1", $country);
        $address2 = new Address("My Address 2", $country);

        $recipient1 = Recipient::create('1ec3d3cf-9919-6446-9a14-0242ac140002', 'Hazem Noor');
        $recipient1->changeAddress($address1);

        $invoice1 = new Invoice($id, $recipient1);

        $recipient2 = Recipient::create('1ec3d3cf-9919-6a5e-9bf5-0242ac140002', 'Hossam Noor');
        $recipient2->changeAddress($address2);
        $invoice2 = new Invoice($id, $recipient2);

        $invoices = [$invoice1, $invoice2];

        $invoiceRepository = new InvoiceRepository();
        foreach ($invoices as $invoice) {
            $invoiceRepository->save($invoice);
        }

        $this->checkStorage($invoices, $invoiceRepository);

        return $invoiceRepository;
    }

    /**
     * @test
     * @depends it_test_save_invoices
     */
    public function it_test_delete_invoices(InvoiceRepositoryInterface $invoiceRepository)
    {
        $invoices = $invoiceRepository->getAll();

        foreach ($invoices as $invoice) {
            $invoiceRepository->delete($invoice);
        }

        $this->checkStorage([], $invoiceRepository);
    }

    /**
     * @param Invoice[]                  $invoices
     * @param InvoiceRepositoryInterface $invoiceRepository
     */
    private function checkStorage(array $invoices, InvoiceRepositoryInterface $invoiceRepository)
    {
        $storage = $invoiceRepository->getAll();

        $ids = array_unique(
            array_map(function (Invoice $invoice): string {
                return $invoice->getId()->getValue();
            }, $invoices)
        );

        $this->assertSameSize($ids, $storage);

        foreach ($invoices as $invoice) {
            $invoiceFound = $invoiceRepository->findById($invoice->getId()->getValue());

            $this->assertInstanceOf(Invoice::class, $invoiceFound);
            if (!is_null($invoiceFound)) {
                $this->assertEquals($invoice->getId(), $invoiceFound->getId());
            }
        }
    }
}
