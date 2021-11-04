<?php

namespace Invoicing\Domain\Repositories;

use Invoicing\Domain\Models\Recipient;

interface RecipientRepository
{
    /**
     * @return Recipient[]
     */
    public function getAll(): array;

    public function findById(string $id): ?Recipient;

    public function save(Recipient $recipient): Recipient;

    public function delete(Recipient $recipient);
}
