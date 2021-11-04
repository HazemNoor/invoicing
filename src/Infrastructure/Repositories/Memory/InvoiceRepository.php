<?php

namespace Invoicing\Infrastructure\Repositories\Memory;

use Invoicing\Domain\Models\Invoice;
use Invoicing\Domain\Repositories\InvoiceRepository as InvoiceRepositoryInterface;

class InvoiceRepository implements InvoiceRepositoryInterface
{
    private array $storage = [];

    public function getAll(): array
    {
        return $this->storage;
    }

    public function findById(string $id): ?Invoice
    {
        return $this->storage[$id] ?? null;
    }

    public function save(Invoice $invoice): Invoice
    {
        $this->storage[$invoice->getId()->getValue()] = $invoice;

        return $invoice;
    }

    public function delete(Invoice $invoice)
    {
        if (!is_null($this->findById($invoice->getId()->getValue()))) {
            unset($this->storage[$invoice->getId()->getValue()]);
        }
    }
}
