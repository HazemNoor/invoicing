<?php

namespace Invoicing\Infrastructure\Repositories\Memory;

use Invoicing\Domain\Models\Country;
use Invoicing\Domain\Repositories\CountryRepository as CountryRepositoryInterface;

class CountryRepository implements CountryRepositoryInterface
{
    private array $storage = [];

    public function getAll(): array
    {
        return $this->storage;
    }

    public function findById(string $id): ?Country
    {
        return $this->storage[$id] ?? null;
    }

    public function save(Country $country): Country
    {
        $this->storage[$country->getId()] = $country;

        return $country;
    }

    public function delete(Country $country)
    {
        if (!is_null($this->findById($country->getId()))) {
            unset($this->storage[$country->getId()]);
        }
    }
}
