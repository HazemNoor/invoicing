<?php

namespace Invoicing\Domain\Repositories;

use Invoicing\Domain\Models\Item;

interface ItemRepository
{
    /**
     * @return Item[]
     */
    public function getAll(): array;

    public function findById(string $id): ?Item;

    public function save(Item $item): Item;

    public function delete(Item $item);
}
