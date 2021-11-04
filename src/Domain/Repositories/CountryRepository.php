<?php

namespace Invoicing\Domain\Repositories;

use Invoicing\Domain\Models\Country;

interface CountryRepository
{
    /**
     * @return Country[]
     */
    public function getAll(): array;

    public function findById(string $id): ?Country;

    public function save(Country $country): Country;

    public function delete(Country $country);
}
