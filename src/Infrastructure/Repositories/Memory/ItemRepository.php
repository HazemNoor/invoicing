<?php

namespace Invoicing\Infrastructure\Repositories\Memory;

use Invoicing\Domain\Models\Item;
use Invoicing\Domain\Repositories\ItemRepository as ItemRepositoryInterface;

class ItemRepository implements ItemRepositoryInterface
{
    private array $storage = [];

    public function getAll(): array
    {
        return $this->storage;
    }

    public function findById(string $id): ?Item
    {
        return $this->storage[$id] ?? null;
    }

    public function save(Item $item): Item
    {
        $this->storage[$item->getId()->getValue()] = $item;

        return $item;
    }

    public function delete(Item $item)
    {
        if (!is_null($this->findById($item->getId()->getValue()))) {
            unset($this->storage[$item->getId()->getValue()]);
        }
    }
}
