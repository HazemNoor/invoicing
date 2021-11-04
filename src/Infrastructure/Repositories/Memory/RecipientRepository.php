<?php

namespace Invoicing\Infrastructure\Repositories\Memory;

use Invoicing\Domain\Models\Recipient;
use Invoicing\Domain\Repositories\RecipientRepository as RecipientRepositoryInterface;

class RecipientRepository implements RecipientRepositoryInterface
{
    private array $storage = [];

    public function getAll(): array
    {
        return $this->storage;
    }

    public function findById(string $id): ?Recipient
    {
        return $this->storage[$id] ?? null;
    }

    public function save(Recipient $recipient): Recipient
    {
        $this->storage[$recipient->getId()->getValue()] = $recipient;

        return $recipient;
    }

    public function delete(Recipient $recipient)
    {
        if (!is_null($this->findById($recipient->getId()->getValue()))) {
            unset($this->storage[$recipient->getId()->getValue()]);
        }
    }
}
