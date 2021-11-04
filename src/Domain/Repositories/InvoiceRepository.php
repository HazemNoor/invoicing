<?php

namespace Invoicing\Domain\Repositories;

use Invoicing\Domain\Models\Invoice;

interface InvoiceRepository
{
    /**
     * @return Invoice[]
     */
    public function getAll(): array;

    public function findById(string $id): ?Invoice;

    public function save(Invoice $invoice): Invoice;

    public function delete(Invoice $invoice);
}
